<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Coche;
use App\Models\Marca;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Stringable;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $token = request()->bearerToken();
        $usuarioValido = Usuario::where('token', '=', $token)->where('es_admin', '=', 1)->first();

        if (!$usuarioValido) {
            return response()->json(['error' => 'No se proporcionó un token válido'], 401);
        }

        $usuarios = Usuario::orderBy('id')->get();
        return response()->json($usuarios);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'edad' => 'required|numeric|min:18',
            'nif' => 'required|min:9|max:9|unique:usuarios,nif',
            'correo-electronico' => 'required|unique:usuarios,correo_electronico',
            'password' => 'required|min:3|max:12'
        ]);

        $usuario = new Usuario();
        $usuario->nombre = $request->input("nombre");
        $usuario->edad = $request->input("edad");
        $usuario->nif = $request->input("nif");
        $usuario->correo_electronico = $request->input("correo-electronico");
        $usuario->password = $request->input("password");
        $ruta = $request->file('imagen')->store('/public/imagenes');
        $ruta = str_replace('public', '/storage', $ruta);
        $usuario->ruta_img = $ruta;
        $usuario->token = Str::random(40); // Agrega un String aleatorio de 40 caracteres

        $usuario->save();

        return response($usuario, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function show(Usuario $usuario)
    {
        $token = request()->bearerToken();
        if ($token) {
            $usuarioValido = Usuario::where('token', '=', $token)->first();
            $cochesUsuario = Coche::where('usuario_id', $usuario->id)->get();
            $marcaCoche = Marca::all();
            if ($usuarioValido->es_admin == 1 || $usuarioValido->id == $usuario->id) {
                return response()->json(['usuario' => $usuario, 'coches' => $cochesUsuario, 'marcas' => $marcaCoche], 200);
            } else {
                return response()->json(['error' => 'No se proporcionó un token válido o no es administrador'], 401);
            }
        } else {
            return response()->json(['error' => 'No se proporcionó un token válido'], 401);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Usuario $usuario)
    {

        $token = request()->bearerToken();
        $usuarioValido = Usuario::where('token', '=', $token)->where('es_admin', '=', 1)->first();

        if (!$usuarioValido) {
            return response()->json(['error' => 'No se proporcionó un token válido o no es administrador'], 401);
        }

        if (!$usuario) {
            return response()->json(['mensaje' => 'El usuario no existe'], 404);
        }

        $request->validate([
            'nombre' => 'required',
            'edad' => 'required|numeric|min:18',
            'nif' => 'required|min:9|max:9|unique:usuarios,nif,' . $usuario->id . ',id',
            'correo-electronico' => 'required|unique:usuarios,correo_electronico,' . $usuario->id . ',id',
            'password' => 'required|min:3|max:12',
        ]);

        $usuario->nombre = $request->nombre;
        $usuario->edad = $request->edad;
        $usuario->nif = $request->nif;
        $usuario->correo_electronico = $request->input("correo-electronico");
        $usuario->password = $request->password;

        if ($request->hasFile('imagen')) {
            $ruta = $request->file('imagen')->store('/public/imagenes');
            $usuario->ruta_img = Storage::url($ruta);
        } else {
            $usuario->ruta_img = $usuario->ruta_img;
        }

        $usuario->save();

        return response("Actualizado");
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function destroy(Usuario $usuario)
    {
        if (!$usuario) {
            return response()->json(['mensaje' => 'El usuario no existe'], 404);
        }

        $token = request()->bearerToken();
        $usuarioValido = Usuario::where('token', '=', $token)->where('es_admin', '=', 1)->first();

        if (!$usuarioValido) {
            return response()->json(['error' => 'No se proporcionó un token válido o no es administrador'], 401);
        }

        $usuario->delete();

        return response()->json(['mensaje' => 'usuario eliminado correctamente']);
    }

    public function eliminados()
    {
        $token = request()->bearerToken();
        $usuarioValido = Usuario::where('token', '=', $token)->where('es_admin', '=', 1)->first();

        if (!$usuarioValido) {
            return response()->json(['error' => 'No se proporcionó un token válido'], 401);
        }

        $usuarios = Usuario::withTrashed()->where('deleted_at', '!=', null)->get();

        return response()->json($usuarios);
    }

    public function restaurarUsuario($usuario)
    {
        $token = request()->bearerToken();
        $usuarioValido = Usuario::where('token', '=', $token)->where('es_admin', '=', 1)->first();

        if (!$usuarioValido) {
            return response()->json(['error' => 'No se proporcionó un token válido'], 401);
        }

        $usuario = Usuario::withTrashed()->find($usuario);

        $usuario->restore();
    }
}
