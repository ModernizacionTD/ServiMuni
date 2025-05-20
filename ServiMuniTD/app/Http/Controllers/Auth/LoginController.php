<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\DataService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    protected $dataService;

    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = $this->dataService->getByEmail($request->email);

        if (!$user) {
            return back()->withErrors([
                'email' => 'No se encontró un funcionario con este correo electrónico.',
            ]);
        }

        // Verificación simple de contraseña
        // NOTA: En producción, NO deberías almacenar contraseñas en texto plano
        if ($request->password != $user['password']) {
            return back()->withErrors([
                'password' => 'La contraseña es incorrecta.',
            ]);
        }

        // Crear una sesión para el usuario
        session([
            'user_id' => $user['id'],
            'user_email' => $user['email'],
            'user_nombre' => $user['nombre'],
            'user_rol' => $user['rol'],
        ]);

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect('/');
    }
}