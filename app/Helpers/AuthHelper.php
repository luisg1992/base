<?php

use Illuminate\Support\Facades\Hash;

if (!function_exists('validacion_password')) {
    function validacion_password($request): array
    {
        if ($request->input('verify_password')) {
            $user = auth()->user();
            if (!$user || !Hash::check($request->input('password'), $user->password)) {
                return [
                    'success' => false,
                    'mensaje' => 'LA CONTRASEÑA INGRESAS ES INCORRECTA, VERIFIQUE LA INFORMACIÓN INGRESADA'
                ];
            }
        }
        return [
            'success' => true,
        ];
    }
}
