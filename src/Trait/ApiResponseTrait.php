<?php

namespace App\Trait;

use Symfony\Component\HttpFoundation\JsonResponse;

trait ApiResponseTrait
{
    public function success(mixed $data, int $status = 200): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    public function error(string $message, int $status = 400): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'message' => $message,
        ], $status);
    }

    public function validationError(array $errors): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'errors' => $errors,
        ], 422);
    }
}