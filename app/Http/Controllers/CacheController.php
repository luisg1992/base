<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class CacheController extends Controller
{
    public function all()
    {
        $helpersPath = app_path('Cache');
        $files = File::allFiles($helpersPath);

        $cacheData = [];

        foreach ($files as $file) {
            $relativePath = $file->getRelativePath();
            $depth = substr_count($relativePath, DIRECTORY_SEPARATOR);
            if ($depth > 1) {
                continue;
            }
            $namespace = 'App\\Cache';
            if (!empty($relativePath)) {
                $subNamespace = str_replace('/', '\\', $relativePath);
                $namespace .= '\\' . $subNamespace;
            }
            $className = $namespace . '\\' . $file->getFilenameWithoutExtension();
            if (class_exists($className) && method_exists($className, 'getCache')) {
                $key = lcfirst(class_basename($className));
                $cacheData[$key] = $className::getCache();
            }
        }

        return response()->json($cacheData);
    }
}
