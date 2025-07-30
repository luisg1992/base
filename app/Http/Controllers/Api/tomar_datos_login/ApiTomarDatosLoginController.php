<?php

namespace App\Http\Controllers\Api\tomar_datos_login;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiTomarDatosLoginController extends Controller
{
    public function obtener_datos_equipo_login(Request $request)
    {
        try {
            $request->validate([
                "TerminalLogin" => "required",
                "IpTerminalLogin" => "required",
                "MacLogin" => "required",
            ]);

            $usuario = User::find($request->IdUsuario);
            if ($usuario) {
                $usuario->TerminalLogin = $request->TerminalLogin;
                $usuario->IpTerminalLogin = $request->IpTerminalLogin;
                $usuario->MacLogin = $request->MacLogin;
                $usuario->EstaEnLinea = 1;
                $usuario->UltimoInicioSesion = now();
                $usuario->save();

                return response()->json([
                    "status" => true,
                    "msg" => 'EL USUARIO ACTUALIZADO EXITOSAMENTE'
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "msg" => 'EL USUARIO INGRESADO NO EXISTE, COMUNÍQUESE CON EL ÁREA DE INFORMÁTICA.'
                ], 500);
            }
        } catch (\Exception $th) {
            return response()->json([
                "status" => false,
                "msg" => $th->getMessage()
            ], 500);
        }
    }
}
