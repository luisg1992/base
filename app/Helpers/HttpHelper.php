<?php

namespace App\Helpers;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;

class HttpHelper
{
    /**
     * Reconstruye un CookieJar desde un array serializado.
     */
    public static function restoreCookieJar(array $cookieArray): CookieJar
    {
        $cookieJar = new CookieJar();

        foreach ($cookieArray as $cookieData) {
            $cookie = new SetCookie([
                'Name'     => $cookieData['Name'],
                'Value'    => $cookieData['Value'],
                'Domain'   => $cookieData['Domain'],
                'Path'     => $cookieData['Path'],
                'Secure'   => $cookieData['Secure'],
                'HttpOnly' => $cookieData['HttpOnly'],
                'Expires'  => $cookieData['Expires'],
            ]);

            $cookieJar->setCookie($cookie);
        }

        return $cookieJar;
    }
}
