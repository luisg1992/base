<?php

use Illuminate\Http\JsonResponse;

if (!function_exists('obtener_respuesta_eliminar_record')) {
    function obtener_respuesta_eliminar_record($title, $message, $record = []): JsonResponse
    {
        return response()->json([
            'title' => $title,
            'verify_password' => $message,
            'record' => $record
        ]);
    }
}

if (!function_exists('obtener_respuesta_exito')) {
    function obtener_respuesta_exito($message, $data = []): JsonResponse
    {
        return response()->json([
            'success' => true,
            'mensaje' => $message,
            'data' => $data
        ]);
    }
}

if (!function_exists('obtener_respuesta_error')) {
    function obtener_respuesta_error($message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'mensaje' => $message
        ]);
    }
}
