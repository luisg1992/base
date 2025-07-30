<?php

namespace App\Http\Controllers\Api\VerificarFirmaDigital;

use CURLFile;
use DiDom\Document;

class ApiVerificarFirmaDigitalController
{
    public function validar()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apps.firmaperu.gob.pe/web/validador.xhtml',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('jakarta.faces.source' => 'j_idt33:j_idt36',
                'jakarta.faces.partial.execute' => 'j_idt33:j_idt36 j_idt33:j_idt36',
                'primefaces.nonce' => 'YjJhNmU2ZGYtODcyYS00ZWE5LTg2NTUtOTEzODBiMDhlNzRi',
                'j_idt33:j_idt36_input' => new CURLFile(public_path('firmas/Receipt-2605-3924.pdf')),
                'jakarta.faces.ViewState' => '7IvNvaiU9EJx42z8z/PySjQj18Av1Vwalfm+Bpgnt/aY3r3ZJ0qvph2iTqTvc3gwU0xMyCLr20eJMhfQ6avQk+05IchHCipauhWyJ1wRTXluWbS14GFky/01jt4782HiPQwntlY6uB0gGZ4uMGzKC6A/IkZ1j4WrYMtPZ7+ihzxWXdHbdn6sSktys8JO+1abGTPafJLiRrUm5PgmtZ7XIlzd6FRr/qwp8Ikec1zhK2Og1yMTKTbNXkCk+rSk4P8RRiJJkDFL/PAgyU7JjK82BHOF21W9LLfKiUGd5RSHe4hls2zh61iMdArcOR4x4gaQ7UNWy/kzpqyfK0SRqldDlTOlNAfNjwzLjGL3TON8lD4f/n9CMbj5OmpD3lpMnxghdS+AIawxRCuFlz3qhe/Gou5loeT62qz0QTf4pGTqBGuGOMh/GGUQbYvbhRRsmLU6UR0kjY3L/mbdmAuV28qe6ZZ9VclAknK57Bnl8XPSh6MMU9xwwBwrZrj7NPI2WAZ2X72VmX4Ek9gWzEl8xVvt1T8xd4JSfn2NqHdKXiSSvLp7MdOjjmRqE4fsZ3WxIcWelRAyf/45Ipjq0GCJd7NNrmJTrnb6bK61b8CNbiFWuOfvLlRUCatbEM15OTitiPxdKhbKUtOUV4urOdngfFkVutS4NelLDNsoVZOR6pzjPWG0dbqB2XW/AY46xg3spBzTktLw3Kpomq0xIeo9iqZvqt6gFJO5+ukmTwFfC8FVXyfodk2hy3i3gHLM5BrOUL90TGra/Yr3yrf76GBGkB9hkKjydKS0RPGoe5GXvjo5T5ak9Jv60o4kwobHJjwJtrQS4Rzj2b7hTpqIb1t6JFpWTi9j4uWLCinBWSHRWCfJaMqJgJ8xxrBlq5DfyXh2XRk23xKJSfiVG/sSvgh/6KjkHs8pAI+ZR+c6ZQRD+cqKqHrmwivr6yox6IeW3mJ+hbPcBqG0b+AxzEj1JxIyPCnarbkTr+5VFxQAvqauB80GeNfYmj6wLc993afZxwp03jrlj7kAwBcsNPY+RxQEuvPhrl4T7rfswpSNg+ap5pw3+KJVYzelWIoPlFsNiE37+c0ReBcldGHoz5rZc3Q8eGxrEc6scr23o3zUbvzpNlHkVE46nae5s1U6WI3sktWNKJCHyOz7rCijsR/p4ciCJWKfVbp3mW3zFClAmN807ZwGu0BU/9ltuY5WNsgcvEgGwQ8Yj4we5QmFBMGM1E7+o8oT69K/CDr9GoMyMeFccNbKw4I='),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: multipart/form-data; boundary=----WebKitFormBoundarySMc7BZfcbkRyRAkU',
                'Cookie: JSESSIONID=29D1A65D55CF5AE922605537E9E74675; Path=/; AWSALB=kBM4yKyOvNlgS4maDTrWYnKsks3iRlD//VXjwTCqRw4xnhhX97nXdBJ7IsOtFcjA5+e7QpVKzHzcZ5UEq+GXwKDNpRNh0vvJKrtpY/QVIWP9NVA6nIJ4xUtbsuYT; AWSALBCORS=kBM4yKyOvNlgS4maDTrWYnKsks3iRlD//VXjwTCqRw4xnhhX97nXdBJ7IsOtFcjA5+e7QpVKzHzcZ5UEq+GXwKDNpRNh0vvJKrtpY/QVIWP9NVA6nIJ4xUtbsuYT'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $new_response = str_replace('href="resources', 'href="https://apps.firmaperu.gob.pe/web/resources', $response);
        $new_response = str_replace('type="text/javascript" src="/web', 'type="text/javascript" src="https://apps.firmaperu.gob.pe/web', $new_response);
        $new_response = str_replace('rel="stylesheet" href="/web', 'rel="stylesheet" href="https://apps.firmaperu.gob.pe/web', $new_response);
        $new_response = str_replace('aria-expanded="false" aria-controls="collapse1" class="cursor titulo_verde_collapse collapsed"',
            'aria-expanded="true" aria-controls="collapse1" class="cursor titulo_verde_collapse"', $new_response);

        $new_response = str_replace('class="collapse"','class="show"', $new_response);

        $doc = new Document($new_response);
        $scripts = $doc->find('html head script');
        if (count($scripts) > 5) {
            foreach ($scripts as $script) {
                $doc->find('html head script')[0]->remove();
            }

            $icons = $doc->find('.fas.fa-caret-right');
            foreach ($icons as $icon) {
                $doc->find('.fas.fa-caret-right')[0]->remove();
            }

            $icons = $doc->find('.fas.fa-check');
            foreach ($icons as $icon) {
                $doc->find('.fas.fa-check')[0]->remove();
            }

            $doc->find('html head link')[0]->remove();
            $doc->find('html head link')[0]->remove();

            $doc->find('div.cabecera_datos_navegacion')[0]->remove();
            $doc->find('body header')[0]->remove();
            $doc->find('body footer')[0]->remove();
            $doc->find('body script')[0]->remove();
            $doc->find('#j_idt86')[0]->remove();

            return $doc->html();
        }

        return '<html><body>No se obtuvo respuesta, volver a intentar.</body></html>';
    }
}
