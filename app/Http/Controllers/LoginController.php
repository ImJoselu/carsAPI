<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Usuario;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            //code...
            $request->validate([
                'correo-electronico' => 'required',
                'password' => 'required|min:3'
            ]);

            $correo = $request->input("correo-electronico");
            $password = $request->input("password");
            $usuario = Usuario::where("correo_electronico", $correo)
                ->where("password", $password)->firstOrFail();

            $usuario->token = Str::random(40);
            $usuario->save();
            $usuarioReducido = [
                "id" => $usuario->id,
                "nombre" => $usuario->nombre,
                "rutaImg" => $usuario->ruta_img,
                "esAdmin" => $usuario->es_admin,
                "token" => $usuario->token,
            ];
            return response([
                $usuarioReducido
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response(['mensaje' => 'Usuario no registrado'], 404);
        }
    }

    public function logout(Request $request)
    {

        try {
            //code...
            $token = $request->bearerToken();
            $usuario = Usuario::where('token', '=', $token)->first();

            if (!$usuario) {
                return response(['mensaje' => 'Error al desconectar el usuario'], 500);
            }

            $usuario->token = null;
            $usuario->save();

            return response(['mensaje' => 'Usuario desconectado correctamente'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response($th);
        }
    }
}
