<?php

namespace Prajwal89\LaraClickInsights\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponser
{
    protected function successResponse(array $data = [], $message = null, $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse(string $message, int $code): JsonResponse
    {
        return response()->json([
            'status' => 'fail',
            'message' => $message,
            'data' => null,
        ], $code);
    }
}
