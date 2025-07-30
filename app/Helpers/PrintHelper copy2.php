<?php

use Dompdf\Dompdf;
use Dompdf\Options;


if (!function_exists('func_generate_pdf')) {
    function func_generate_pdf(
        $template_path,
        $template_filename,
        $data = [],
        $format = 'a4',
        $save = false,
        $template_path_save = null
    )
    {
        // Establece un indicador global para marcar la generación del PDF
        request()->merge(['generating_pdf' => true]);

        // Parámetros del PDF según el formato
        $size_width = '21cm';
        $size_height = '29.7cm';

        // Obtén el contenido del archivo CSS
        $css = file_get_contents(public_path('css/template_' . $format . '.css'));

        // Genera el HTML con los estilos embebidos
        $html = '<style>' . $css . '</style>';
        $html .= view($template_path . '/' . $template_filename . '_' . $format, [
            'data' => $data,
        ])->render();

        // Crear instancia de Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);  // Habilitar HTML5
        $options->set('isPhpEnabled', true);          // Habilitar PHP en el HTML si es necesario (cuidado con la seguridad)
        $options->set('isHtml5ParserEnabled', true);  // Parseo HTML5
        $options->set('isPhpEnabled', true);          // Permitir PHP en HTML si necesario

        // Crear el objeto Dompdf
        $pdf = new Dompdf($options);

        // Cargar el contenido HTML en Dompdf
        $pdf->loadHtml($html);
        // Establecer tamaño de la página
        if ($format === 'ticket') {
            $pdf->setPaper([0, 0, 230, 500], 'Portrait'); // Tamaño específico para ticket
        } else {
            $pdf->setPaper($size_width, $size_height); // Tamaño A4 por defecto
        }

        // Renderizar el PDF (esto genera el PDF)
        $pdf->render();

        // Obtener el contenido del PDF en formato binario
        $result = $pdf->output();

        // Verificar si hubo un error en la generación del PDF
        if (!$result) {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => 'Error en la creación del PDF',
            ]);
        }

        // Guardar el archivo PDF si se requiere
        if ($save) {
            if (!empty($result)) {
                crearCarpetas($template_path_save);
                $save_path = public_path($template_path_save . '/' . $data['filename'] . '.pdf');
                file_put_contents($save_path, $result);
            }
        } else {
            // Retornar el PDF como base64 en una respuesta JSON si no se guarda
            return response()->json([
                'success' => true,
                'codigo' => 'OK',
                'pdf_base64' => base64_encode($result),
            ]);
        }


        // Retornar respuesta inmediata sin esperar que el PDF se haya generado
        return response()->json([
            'success' => true,
            'codigo' => 'OK',
            'mensaje' => 'PDF en proceso de generación.',
        ]);
    }
}

if (!function_exists('crearCarpetas')) {
    function crearCarpetas($ruta)
    {
        $carpetas = explode('/', $ruta); // Dividir la ruta en segmentos
        $rutaActual = public_path();    // Inicia desde la carpeta `public`

        foreach ($carpetas as $carpeta) {
            $rutaActual .= '/' . $carpeta;
            if (!is_dir($rutaActual)) { // Verifica si la carpeta no existe
                mkdir($rutaActual, 0777, true); // Crea la carpeta con permisos recursivos
            }
        }
    }
}

if (!function_exists('func_generate_document_pdf')) {
    function func_generate_document_pdf($template_path, $template_filename, $data = [], $format = 'a4')
    {
        $size_width = '21cm';
        $size_height = '29.7cm';
        $margin_top = '1cm';
        $margin_bottom = '2cm';
        $margin_right = '1cm';
        $margin_left = '1cm';

        // Cambiar configuración según el formato 'ticket'
        if ($format === 'ticket') {
            $size_width = '8cm';
            $size_height = '40cm';
            $margin_top = '0cm';
            $margin_bottom = '.5cm';
            $margin_right = '.25cm';
            $margin_left = '.25cm';
        }

        // Generar el contenido HTML usando la vista
        $html = view($template_path . '/' . $template_filename . '_' . $format, [
            'data' => $data,
        ])->render();

        // Path del archivo CSS para la plantilla
        $path_css = public_path('css/template_' . $format . '.css');
        $footer_html = ''; // Si necesitas un pie de página, lo puedes agregar aquí

        // Crear una instancia de Pdf con los parámetros
        $pdf = new mikehaertl\wkhtmlto\Pdf([
            'no-outline',
            'disable-smart-shrinking',
            'dpi' => '96',
            'encoding' => 'UTF-8',
            'margin-top' => $margin_top,
            'margin-bottom' => $margin_bottom,
            'margin-right' => $margin_right,
            'margin-left' => $margin_left,
            'print-media-type',
            'zoom' => '1',
            'viewport-size' => '1280x1024',
            'page-width' => $size_width,
            'page-height' => $size_height,
            'user-style-sheet' => $path_css,
            'footer-html' => $footer_html,
            'no-images',
            'lowquality',
            'disable-javascript',
        ]);

        $pdf->addPage($html);

        if (is_windows()) {
            $pdf->binary = public_path('vendor/wkhtmltopdf.exe');
        }

        $pdf_content = $pdf->toString();

        if (!$pdf_content) {
            return [
                'success' => false,
                'message' => $pdf->getError()
            ];
        }

        return [
            'success' => true,
            'pdf_content' => $pdf_content
        ];
    }
}

if (!function_exists('is_windows')) {
    function is_windows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
}
