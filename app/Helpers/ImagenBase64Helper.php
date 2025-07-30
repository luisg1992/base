<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImagenBase64Helper
{
    public static function almacenarImagenBase64(string $imageBase64, string $folder): array
    {
        try {
            // Asegura que el folder exista
            if (!Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->makeDirectory($folder);
            }

            // Extrae datos reales si el base64 viene con "data:image/..."
            if (str_starts_with($imageBase64, 'data:image')) {
                [$meta, $imageBase64] = explode(',', $imageBase64);
            }

            $uuid = Str::uuid() . '.jpg';
            $relativePath = "{$folder}/{$uuid}";
            $fullPath = Storage::disk('public')->path($relativePath);

            // Decodifica base64 y valida
            $decoded = base64_decode($imageBase64);
            if ($decoded === false) {
                return ['success' => false, 'message' => 'Base64 inválido'];
            }

            // Guarda imagen temporal
            $tempPath = tempnam(sys_get_temp_dir(), 'img_');
            file_put_contents($tempPath, $decoded);

            // Procesa imagen
            $manager = new ImageManager(new Driver());
            $image = $manager->read($tempPath);
            $image->scale(height: 300);
            $image->save($fullPath);

            unlink($tempPath);

            return [
                'success' => true,
                'uuid' => $uuid,
                'path' => "storage/{$relativePath}",
                'url' => asset("storage/{$relativePath}")
            ];
        } catch (\Throwable $e) {
            Log::error('Error al almacenar imagen base64: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al procesar la imagen'];
        }
    }

    public static function obtenerUrlImagen(?string $pathFilename, string $folder, string $default = ''): string
    {
        if (is_null($pathFilename) || $pathFilename === '') {
            return asset($default);
        }

        $path = "{$folder}/{$pathFilename}";

        if (Storage::disk('public')->exists($path)) {
            return asset("storage/{$path}");
        }

        return asset($default);
    }
}
