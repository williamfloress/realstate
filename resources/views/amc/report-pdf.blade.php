<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte AMC - {{ $sectorNombre }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; margin: 20px; }
        h1 { font-size: 16px; margin-bottom: 5px; }
        h2 { font-size: 12px; margin-top: 15px; margin-bottom: 8px; }
        .meta { color: #666; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #333; padding: 3px 4px; text-align: left; font-size: 8px; }
        th { background-color: #eee; font-weight: bold; }
        .resumen { margin-bottom: 20px; }
        .resumen table { width: auto; }
        .resumen td:first-child { font-weight: bold; width: 180px; }
    </style>
</head>
<body>
    <h1>Análisis de Mercado Comparativo (AMC)</h1>
    <p class="meta">Sector: {{ $sectorNombre }} | Fecha: {{ $fecha }}</p>

    <h2>Datos del inmueble en estudio</h2>
    <table class="resumen">
        <tr><td>Área m²</td><td>{{ $input['area_m2'] }}</td></tr>
        <tr><td>Habitaciones</td><td>{{ $input['habitaciones'] }}</td></tr>
        <tr><td>Baños</td><td>{{ $input['banos'] }}</td></tr>
        <tr><td>Parqueos</td><td>{{ $input['parqueos'] }}</td></tr>
    </table>

    <h2>Resultado de la valoración</h2>
    <table class="resumen">
        <tr><td>Valor base</td><td>${{ number_format($result['valorBase'], 2, ',', '.') }}</td></tr>
        <tr><td>Valor con acabados</td><td>${{ number_format($result['valorConAcabados'], 2, ',', '.') }}</td></tr>
        <tr><td>Promedio $/m²</td><td>${{ number_format($result['promedioValorM2'], 2, ',', '.') }}</td></tr>
        <tr><td>Comparables encontrados</td><td>{{ $result['cantidadComparables'] }}</td></tr>
    </table>

    <h2>Propiedades comparables</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Sector</th>
                <th>Precio</th>
                <th>Área m²</th>
                <th>$/m²</th>
                <th>Hab.</th>
                <th>Baños</th>
                <th>Parq.</th>
                <th>Piso</th>
                <th>Cocina</th>
                <th>Baño</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($result['comparables'] ?? [] as $i => $c)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $c['sector']['nombre'] ?? '-' }}</td>
                <td>${{ number_format($c['precio'], 2, ',', '.') }}</td>
                <td>{{ $c['areaConstruccionM2'] }}</td>
                <td>${{ number_format($c['valorM2'], 2, ',', '.') }}</td>
                <td>{{ $c['habitaciones'] }}</td>
                <td>{{ $c['banos'] }}</td>
                <td>{{ $c['parqueos'] }}</td>
                <td>{{ $c['acabadoPiso']['nombre'] ?? '-' }}</td>
                <td>{{ $c['acabadoCocina']['nombre'] ?? '-' }}</td>
                <td>{{ $c['acabadoBano']['nombre'] ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p class="meta" style="margin-top: 20px;">Generado por Umbral | Análisis de Mercado Comparativo</p>
</body>
</html>
