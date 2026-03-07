<?php

namespace App\Http\Controllers;

use App\Http\Requests\RunAmcRequest;
use App\Models\Sector;
use App\Services\AmcService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class AmcExportController extends Controller
{
    public function __construct(
        private AmcService $amcService
    ) {}

    public function exportPdf(RunAmcRequest $request): Response
    {
        $validated = $request->validated();
        $result = $this->amcService->run($validated);

        $sector = Sector::find($validated['sector_id']);
        $sectorNombre = $sector?->nombre ?? ($result['comparables'][0]['sector']['nombre'] ?? 'AMC');

        $pdf = Pdf::loadView('amc.report-pdf', [
            'result' => $result,
            'input' => $validated,
            'sectorNombre' => $sectorNombre,
            'fecha' => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'portrait');

        $filename = 'AMC-' . Str::slug($sectorNombre) . '-' . now()->format('Y-m-d-His') . '.pdf';

        return $pdf->download($filename);
    }
}
