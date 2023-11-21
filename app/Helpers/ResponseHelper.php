<?php
// app/Helpers/ResponseHelper.php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function success($message, $data = null, $status = 200): JsonResponse
    {
        $response = [
            'status'  => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    public static function error($message, $status = 500): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'data'    => null,
        ], $status);
    }
}
