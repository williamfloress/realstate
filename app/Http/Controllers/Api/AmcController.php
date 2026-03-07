<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RunAmcRequest;
use App\Services\AmcService;
use Illuminate\Http\JsonResponse;

class AmcController extends Controller
{
    public function __construct(
        private AmcService $amcService
    ) {}

    public function run(RunAmcRequest $request): JsonResponse
    {
        try {
            $result = $this->amcService->run($request->validated());
            return response()->json($result);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 400);
        }
    }
}
