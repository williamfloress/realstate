<?php

namespace App\Services;

use App\Models\Acabado;
use App\Models\PonderacionAcabado;
use App\Models\Prop\Property;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AmcService
{
    private float $toleranciaAreaPct;

    public function __construct()
    {
        $this->toleranciaAreaPct = (float) config('amc.TOLERANCIA_AREA_PCT', 0.15);
    }

    /**
     * Ejecuta el Análisis de Mercado Comparativo.
     *
     * @param array $input sector_id, area_m2, habitaciones, banos, parqueos, anio_construccion?, finish_piso_id?, finish_cocina_id?, finish_bano_id?
     * @return array
     */
    public function run(array $input): array
    {
        $sectorId = (int) $input['sector_id'];
        $areaM2 = (float) $input['area_m2'];
        $habitaciones = (int) $input['habitaciones'];
        $banos = (int) $input['banos'];
        $parqueos = (int) $input['parqueos'];
        $anioConstruccion = isset($input['anio_construccion']) ? (int) $input['anio_construccion'] : null;
        $finishPisoId = isset($input['finish_piso_id']) ? (int) $input['finish_piso_id'] : null;
        $finishCocinaId = isset($input['finish_cocina_id']) ? (int) $input['finish_cocina_id'] : null;
        $finishBanoId = isset($input['finish_bano_id']) ? (int) $input['finish_bano_id'] : null;

        $aplicaAcabados = $finishPisoId && $finishCocinaId && $finishBanoId;

        if ($aplicaAcabados) {
            $this->validarAcabados($finishPisoId, $finishCocinaId, $finishBanoId);
        }

        $areaMin = $areaM2 * (1 - $this->toleranciaAreaPct);
        $areaMax = $areaM2 * (1 + $this->toleranciaAreaPct);

        $propiedades = Property::with(['sector', 'acabadoPiso', 'acabadoCocina', 'acabadoBano'])
            ->where('sector_id', $sectorId)
            ->get()
            ->filter(function ($p) use ($areaMin, $areaMax) {
                $area = (float) ($p->area_construccion_m2 ?? $p->sqft ?? 0);
                return $area >= $areaMin && $area <= $areaMax && $area > 0;
            })
            ->values();

        if ($propiedades->isEmpty()) {
            throw ValidationException::withMessages([
                'sector_id' => ['No se encontraron propiedades comparables en este sector con el área especificada.'],
            ]);
        }

        $ponderaciones = $this->obtenerPonderaciones();

        $comparables = [];
        $valoresM2 = [];
        $precios = [];
        $promediosAcabado = [];

        foreach ($propiedades as $p) {
            $area = (float) ($p->area_construccion_m2 ?? $p->sqft ?? 0);
            if ($area <= 0) {
                continue;
            }
            $precio = (float) $p->price;
            $valorM2 = $precio / $area;
            $promedioAcabado = $this->promedioAcabadoPonderado(
                $p->acabadoPiso?->puntaje ?? 0,
                $p->acabadoCocina?->puntaje ?? 0,
                $p->acabadoBano?->puntaje ?? 0,
                $ponderaciones['piso'],
                $ponderaciones['cocina'],
                $ponderaciones['bano']
            );

            $comparables[] = [
                'id' => $p->id,
                'sectorId' => $p->sector_id,
                'sector' => $p->sector ? ['id' => $p->sector->id, 'nombre' => $p->sector->nombre] : null,
                'latitud' => $p->latitude,
                'longitud' => $p->longitude,
                'precio' => $this->round2($precio),
                'areaConstruccionM2' => $this->round2($area),
                'valorM2' => $this->round2($valorM2),
                'promedioAcabado' => $this->round2($promedioAcabado),
                'habitaciones' => $p->beds ?? 0,
                'banos' => $p->baths ?? 0,
                'parqueos' => $p->parqueos ?? 0,
                'anioConstruccion' => $p->year_built,
                'estado' => $this->mapearEstado($p->status),
                'acabadoPiso' => $p->acabadoPiso ? ['id' => $p->acabadoPiso->id, 'nombre' => $p->acabadoPiso->nombre, 'puntaje' => $p->acabadoPiso->puntaje] : null,
                'acabadoCocina' => $p->acabadoCocina ? ['id' => $p->acabadoCocina->id, 'nombre' => $p->acabadoCocina->nombre, 'puntaje' => $p->acabadoCocina->puntaje] : null,
                'acabadoBano' => $p->acabadoBano ? ['id' => $p->acabadoBano->id, 'nombre' => $p->acabadoBano->nombre, 'puntaje' => $p->acabadoBano->puntaje] : null,
            ];

            $valoresM2[] = $valorM2;
            $precios[] = $precio;
            $promediosAcabado[] = $promedioAcabado;
        }

        $promedioValorM2 = $this->mean($valoresM2);
        $medianaValorM2 = $this->median($valoresM2);
        $desviacionEstandarValorM2 = $this->std($valoresM2);
        $tasaDesviacion = $promedioValorM2 > 0 ? $desviacionEstandarValorM2 / $promedioValorM2 : 0;

        $valorBase = $this->round2($areaM2 * $promedioValorM2);

        $promedioAcabadoComparables = $this->mean($promediosAcabado);
        $desviacionEstandarAcabados = $this->std($promediosAcabado);
        $tasaDesviacionAcabados = $promedioAcabadoComparables > 0 ? $desviacionEstandarAcabados / $promedioAcabadoComparables : 0;

        $acabadosPiso = Acabado::find($finishPisoId);
        $acabadosCocina = Acabado::find($finishCocinaId);
        $acabadosBano = Acabado::find($finishBanoId);

        $promedioAcabadoSujeto = 0;
        $acabadosSujeto = null;

        if ($aplicaAcabados && $acabadosPiso && $acabadosCocina && $acabadosBano) {
            $promedioAcabadoSujeto = $this->promedioAcabadoPonderado(
                $acabadosPiso->puntaje,
                $acabadosCocina->puntaje,
                $acabadosBano->puntaje,
                $ponderaciones['piso'],
                $ponderaciones['cocina'],
                $ponderaciones['bano']
            );
            $valorConAcabados = $this->valorConAcabados($valorBase, $promedioAcabadoSujeto, $promedioAcabadoComparables);
            $acabadosSujeto = [
                'piso' => ['nombre' => $acabadosPiso->nombre, 'ponderacion' => $ponderaciones['piso']],
                'cocina' => ['nombre' => $acabadosCocina->nombre, 'ponderacion' => $ponderaciones['cocina']],
                'bano' => ['nombre' => $acabadosBano->nombre, 'ponderacion' => $ponderaciones['bano']],
            ];
        } else {
            $valorConAcabados = $valorBase;
        }

        $result = [
            'valorBase' => $valorBase,
            'valorConAcabados' => $this->round2($valorConAcabados),
            'promedioValorM2' => $this->round2($promedioValorM2),
            'medianaValorM2' => $this->round2($medianaValorM2),
            'desviacionEstandarValorM2' => $this->round2($desviacionEstandarValorM2),
            'tasaDesviacion' => $this->round4($tasaDesviacion),
            'cantidadComparables' => count($comparables),
            'promedioPrecioComparables' => $this->round2($this->mean($precios)),
            'medianaPrecioComparables' => $this->round2($this->median($precios)),
            'promedioAcabadoComparables' => $this->round2($promedioAcabadoComparables),
            'desviacionEstandarAcabados' => $this->round2($desviacionEstandarAcabados),
            'tasaDesviacionAcabados' => $this->round4($tasaDesviacionAcabados),
            'comparables' => $comparables,
        ];

        if ($aplicaAcabados) {
            $result['promedioAcabadoSujeto'] = $this->round2($promedioAcabadoSujeto);
            $result['acabadosSujeto'] = $acabadosSujeto;
        }

        return $result;
    }

    private function validarAcabados(int $pisoId, int $cocinaId, int $banoId): void
    {
        $piso = Acabado::where('id', $pisoId)->where('tipo', 'piso')->first();
        $cocina = Acabado::where('id', $cocinaId)->where('tipo', 'cocina')->first();
        $bano = Acabado::where('id', $banoId)->where('tipo', 'bano')->first();

        if (!$piso || !$cocina || !$bano) {
            throw ValidationException::withMessages([
                'finish_piso_id' => ['Los IDs de acabados deben existir y corresponder a su tipo (piso, cocina, baño).'],
            ]);
        }
    }

    private function obtenerPonderaciones(): array
    {
        $rows = PonderacionAcabado::all();
        $defaults = ['piso' => 7, 'cocina' => 3, 'bano' => 4];
        foreach ($rows as $r) {
            $defaults[$r->tipo] = $r->ponderacion;
        }
        return $defaults;
    }

    private function mapearEstado(?string $status): string
    {
        return match ($status) {
            'active' => 'disponible',
            'reserved' => 'reservada',
            'rented' => 'alquilada',
            'sold' => 'vendida',
            default => $status ?? 'disponible',
        };
    }

    private function mean(array $arr): float
    {
        if (empty($arr)) {
            return 0.0;
        }
        return array_sum($arr) / count($arr);
    }

    private function median(array $arr): float
    {
        if (empty($arr)) {
            return 0.0;
        }
        $sorted = $arr;
        sort($sorted);
        $mid = (int) floor(count($sorted) / 2);
        return count($sorted) % 2 !== 0
            ? $sorted[$mid]
            : ($sorted[$mid - 1] + $sorted[$mid]) / 2;
    }

    private function std(array $arr): float
    {
        if (count($arr) <= 1) {
            return 0.0;
        }
        $m = $this->mean($arr);
        $variance = array_reduce($arr, fn ($s, $x) => $s + ($x - $m) ** 2, 0.0) / (count($arr) - 1);
        return sqrt($variance);
    }

    private function promedioAcabadoPonderado(
        float $puntajePiso,
        float $puntajeCocina,
        float $puntajeBano,
        float $pesoPiso,
        float $pesoCocina,
        float $pesoBano
    ): float {
        $pesoTotal = $pesoPiso + $pesoCocina + $pesoBano;
        if ($pesoTotal === 0.0) {
            return 0.0;
        }
        return ($puntajePiso * $pesoPiso + $puntajeCocina * $pesoCocina + $puntajeBano * $pesoBano) / $pesoTotal;
    }

    private function valorConAcabados(float $valorBase, float $promedioAcabadoSujeto, float $promedioAcabadoComparables): float
    {
        if ($promedioAcabadoComparables <= 0) {
            return $valorBase;
        }
        return $valorBase * ($promedioAcabadoSujeto / $promedioAcabadoComparables);
    }

    private function round2(float $x): float
    {
        return round($x, 2);
    }

    private function round4(float $x): float
    {
        return round($x, 4);
    }
}
