<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Api\impresion\ApiAuditoriaImpresionController;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Api\Http\Controllers\Sms\SmsController;
use Modules\Auditoria\Models\Login\LoginSession;
use Modules\Core\Http\Controllers\ModuloController;
use Modules\Core\Models\Parametro;

class LoginController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Login', [
            'errors' => session('errors') ? session('errors')->getBag('default')->all() : [],
        ]);
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        if (!Auth::attempt($credentials)) {
            $this->registrarIntentoFallido($request);

            return response()->json([
                'success' => false,
                'message' => 'Las credenciales proporcionadas no son válidas.',
            ]);
        }

        $request->session()->regenerate();
        $this->registrarSesionLogin($request);
        $user = auth()->user();
        $dataJson = [
            'action' => 'obtener_datos_equipo_login',
            'IdUsuario' => $user->id,
        ];

        //$response = (new ApiAuditoriaImpresionController())->store($dataJson)->getData(true);
        //$externalId = $response['success'] ? $response['data']['external_id'] : null;

        $parametro = Parametro::query()->where('Codigo', 'SMS_LOGIN')->first();
        if ($parametro && $parametro->ValorTexto === 'S') {
            (new SmsController())->sendByFormParams([
                'title' => 'Inicio de sesión',
                'body' => 'Hola, ' . $user->email . ' acaba de inicar sesión en el sistema ' . config('app.name'),
                'phone' => $user->Celular,
            ]);
        }
        $data = [
            'success' => true,
            'data' => [
                'externalId' => 1,
            ]
        ];
        return response()->json($data);
    }


    public function obtener_datos_equipo_login(Request $request)
    {
        try {
            $request->validate([
                "TerminalLogin" => "required",
                "IpTerminalLogin" => "required",
                "MacLogin" => "required",
            ]);
            return response()->json([
                "status" => true,
                "msg" => 'EL USUARIO ACTUALIZADO EXITOSAMENTE'
            ]);
        } catch (\Exception $th) {
            return response()->json([
                "status" => false,
                "msg" => $th->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        $user = auth()->user();
        $this->registrarSesionCierre($request);
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $parametro = Parametro::query()->where('Codigo', 'SMS_LOGOUT')->first();
        if ($parametro && $parametro->ValorTexto === 'S') {
            (new SmsController())->sendByFormParams([
                'title' => 'Inicio de sesión',
                'body' => 'Nos vemos, ' . $user->email . ' acaba de cerrar sesión en el sistema ' . config('app.name'),
                'phone' => $user->Celular,
            ]);
        }

        return redirect('/login');
    }

    public function getMenu()
    {
        return (new ModuloController())->getRecords();
    }

    protected function registrarSesionLogin(Request $request)
    {
        LoginSession::query()
            ->create([
                'user_id' => Auth::id(),
                'direccion_ip' => $request->ip(),
                'agente_usuario' => $request->userAgent(),
                'hora_inicio_sesion' => now(),
                'ultimo_intento_at' => now(),
                'razon' => 'Inicio de Sesión Exitoso - ' . $request->input('email'),
            ]);
    }

    protected function registrarIntentoFallido(Request $request)
    {
        $user = User::query()
            ->where('email', $request->input('email'))
            ->first();
        $userId = $user ? $user->id : null;
        LoginSession::query()
            ->create([
                'user_id' => $userId,
                'direccion_ip' => $request->ip(),
                'agente_usuario' => $request->userAgent(),
                'razon' => 'Error de credenciales - ' . $request->input('email'),
                'hora_inicio_sesion' => now(),
                'ultimo_intento_at' => now(),
            ]);
    }

    protected function registrarSesionCierre(Request $request)
    {
        $sesionLogin = LoginSession::query()
            ->where('user_id', Auth::id())
            ->whereNull('hora_cierre_sesion')
            ->first();
        if ($sesionLogin) {
            $sesionLogin->update([
                'hora_cierre_sesion' => now(),
                'razon' => 'Cierre de Sesión Exitoso',
            ]);
        }
    }
}
