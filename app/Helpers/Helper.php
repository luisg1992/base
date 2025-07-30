<?php

use Illuminate\Http\Client\Response;

if (!function_exists('formatear_fecha')) {
    function formatear_fecha($fecha)
    {
        if (is_null($fecha) || $fecha === '') {
            return null;
        }

        if ($fecha instanceof \DateTimeInterface) {
            return $fecha->format('Y-m-d');
        }

        $formatos = ['d/m/Y', 'Y-m-d', 'Y-m-d\TH:i:sP', 'Y-m-d H:i:s'];

        foreach ($formatos as $formato) {
            $dt = \DateTime::createFromFormat($formato, $fecha);
            if ($dt && $dt->format($formato) === $fecha) {
                return $dt->format('Y-m-d');
            }
        }

        try {
            return \Carbon\Carbon::parse($fecha)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}


if (!function_exists('str_to_upper_utf8')) {
    function str_to_upper_utf8($text)
    {
        if (is_null($text)) {
            return null;
        }
        return mb_strtoupper(trim($text), 'utf-8');
    }
}

if (!function_exists('str_to_lower_utf8')) {
    function str_to_lower_utf8($text)
    {
        if (is_null($text)) {
            return null;
        }
        return mb_strtolower(trim($text), 'utf-8');
    }
}

if (!function_exists('validar_dni')) {
    function validar_dni($number)
    {
        if (!preg_match('/^\d{8}(?:[-\s]\d{4})?$/', $number)) {
            return false;
        }

        return true;
    }
}

if (!function_exists('convert_to_utf8_from_response')) {
    function convert_to_utf8_from_response(Response $response): string
    {
        // Detectar charset desde Content-Type
        $contentType = $response->header('Content-Type');

        // Ej: text/html; charset=ISO-8859-1
        preg_match('/charset=([a-zA-Z0-9\-]+)/i', $contentType, $matches);
        $sourceEncoding = $matches[1] ?? 'Windows-1252';

        // Convertir con iconv (más confiable que mb_convert_encoding)
        return iconv($sourceEncoding, 'UTF-8//IGNORE', $response->body());
    }
}

if (!function_exists('remove_accents')) {
    function remove_accents($text)
    {
        $search = ['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú'];
        $replace = ['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U'];

        return str_replace($search, $replace, $text);
    }
}


if (!function_exists('hidden_text')) {
    function hidden_text($text, $showText = 4)
    {
        $length = strlen($text);
        if ($length <= $showText) return str_repeat('*', $length);

        return str_repeat('*', $length - $showText) . substr($text, -$showText);
    }
}

if (!function_exists('hidden_text_left')) {
    function hidden_text_left($text, $showText = 4)
    {
        $length = strlen($text);
        if ($length <= $showText) return str_repeat('*', $length);

        return substr($text, 0, $showText) . str_repeat('*', $length - $showText);
    }
}

if (!function_exists('hidden_email')) {
    function hidden_email($email, $left = 2, $final = 2)
    {
        // Validar que sea un correo electrónico válido
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email; // o puedes retornar '', false o lanzar excepción
        }

        list($name, $domain) = explode('@', $email);
        $length = strlen($name);

        if ($length <= ($left + $final)) {
            return str_repeat('*', $length) . '@' . $domain;
        }

        $hidden = substr($name, 0, $left) . str_repeat('*', $length - ($left + $final)) . substr($name, -$final);
        return $hidden . '@' . $domain;
    }
}

if (!function_exists('isValidCellphone')) {
    function isValidCellphone(string $number): bool
    {
        return preg_match('/^9\d{8}$/', $number) === 1;
    }
}

if (!function_exists('isValidEmail')) {
    function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
