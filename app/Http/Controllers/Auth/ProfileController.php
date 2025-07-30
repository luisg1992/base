<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Modules\Api\Http\Controllers\Sms\SmsController;
use Modules\Persona\Models\Empleado;
use Throwable;

class ProfileController extends Controller
{
    public function getProfile()
    {
        $user = auth()->user();
        return [
            'cellphone' => $user->Celular,
            'email' => $user->CorreoElectronico,
        ];
    }

    public function storeProfile(Request $request)
    {
        $request->validate([
            'password' => 'required|string|confirmed',
            'password_confirmation' => 'required|string',
            'email' => 'required|email',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->input('password_old'), $user->password)) {
            return [
                'success' => false,
                'message' => 'Contraseña actual incorrecta'
            ];
        }

        $code = Cache::get('code_' . $user->IdEmpleado);

        if ($request->input('code') !== $code) {
            return [
                'success' => false,
                'message' => 'Código de verificación incorrecta'
            ];
        }

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return [
            'success' => true,
            'message' => 'Contraseña actualizada'
        ];
    }

    public function sendCode(Request $request)
    {
        $cellphone = $request->input('cellphone');
        $email = $request->input('email');

        if (is_null($email) || $email === '' || !isValidEmail($email)) {
            return [
                'success' => false,
                'message' => 'Correo electrónico inválido'
            ];
        }

        if ($cellphone !== '' && !is_null($cellphone)) {
            if (!isValidCellphone($cellphone)) {
                return [
                    'success' => false,
                    'message' => 'Número de celular inválido'
                ];
            }
        }

        $code = str_pad(rand(99, 999999), 6, '0', STR_PAD_LEFT);
        $user = auth()->user();
        $user->update([
            'Celular' => $cellphone,
            'CorreoElectronico' => $email,
        ]);
        Cache::put('code_' . $user->IdEmpleado, $code, 600);

        $this->sendSms($code, $cellphone);

        return $this->sendEmail($code, $email);
    }

    public function sendCodeResetPassword(Request $request)
    {
        $empleadoId = $request->input('empleado_id');

        $person = Empleado::query()
            ->with('usuarioRelacion')
            ->find($empleadoId);

        $email = $person->usuarioRelacion->CorreoElectronico;

        $code = str_pad(rand(99, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put('code_' . $empleadoId, $code, 600);

        return $this->sendEmail($code, $email);
    }

    public function validateNumber(Request $request)
    {
        $number = $request->input('number');
        $person = Empleado::query()
            ->with('usuarioRelacion')
            ->where('DNI', $number)->first();
        if ($person) {
            return [
                'success' => true,
                'data' => [
                    'empleado_id' => $person->IdEmpleado,
                    'full_name' =>
                        hidden_text_left($person->ApellidoPaterno, 2) . ' ' .
                        hidden_text_left($person->ApellidoMaterno, 2) . ', ' .
                        hidden_text_left($person->Nombres, 2),
                    'email' => $person->usuarioRelacion ? hidden_email($person->usuarioRelacion->CorreoElectronico) : '',
                ]
            ];
        }

        return [
            'success' => false,
            'message' => 'El número de documento ingresado no existe'
        ];
    }

    public function storeResetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|confirmed',
            'password_confirmation' => 'required|string',
            'email' => 'required|email',
        ]);


        $person_id = $request->input('empleado_id');
        $person = Empleado::query()
            ->with('usuarioRelacion')
            ->find($person_id);

        if (!$person->usuarioRelacion) {
            return [
                'success' => false,
                'message' => 'El usuario no existe'
            ];
        }

        $code = Cache::get('code_' . $person->IdEmpleado);

        if ($request->input('code') !== $code) {
            return [
                'success' => false,
                'message' => 'Código de verificación incorrecta'
            ];
        }

        $person->usuarioRelacion->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return [
            'success' => true,
            'message' => 'Contraseña actualizada'
        ];
    }

    public function sendEmail($code, $email)
    {
        $subject = 'Código de verificación';
        $message = "{$code} es su código de seguridad.";

        $data = [
            'email' => $email,
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
            'subject' => $subject,
            'message' => $message,
            'number' => null,
            'name' => null,
            'template' => 'mail',
            'attach' => false,
            'file_base64' => null,
            'filename' => null,
        ];

        try {
            Mail::to($email)->send(new SendMail($data));
            return [
                'success' => true,
                'code' => 0,
                'message' => 'Código enviado satisfactoriamente',
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        }
    }

    public function sendSms($code, $cellphone)
    {
        $data = [
            'title' => 'Código de verificación',
            'body' => "{$code} es su código de seguridad.",
            'phone' => $cellphone
        ];

        (new SmsController())->sendByFormParams($data);
    }
}
