<?php

namespace App\Http\Controllers;

use App\Models\Coche;
use App\Models\Marca;
use App\Models\Usuario;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CocheController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $marcas = Marca::orderBy('id')->get();
        $coches = Coche::orderBy('usuario_id')->get();

        $indexArray = [
            'marcas' => $marcas,
            'coches' => $coches
        ];

        return response($indexArray, 200);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //
        $token = request()->bearerToken();
        $usuarioValido = Usuario::where('token', '=', $token)->where('es_admin', '=', 1)->first();

        if (!$usuarioValido) {
            return response()->json(['error' => 'No se proporcionó un token válido o no es administrador'], 401);
        }

        $request->validate([

            'matricula' => 'required|string',
            'modelo' => 'required|string',
            'color' => 'required|string',
            'motor' => 'required|string',
            'marca_id' => 'exists:marcas,id',

        ]);
        $coche = new Coche();
        $coche->matricula = $request->input("matricula");
        $coche->modelo = $request->input("modelo");
        $coche->color = $request->input("color");
        $coche->motor = $request->input("motor");
        $ruta = $request->file('imagen')->store('/public/imagenes');
        $ruta = str_replace('public', '/storage', $ruta);
        $coche->ruta_img = $ruta;
        $coche->marca_id = $request->input("marca_id");

        $coche->save();

        return response($coche, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Coche  $coche
     * @return \Illuminate\Http\Response
     */
    public function show(Coche $coche)
    {
        $marcas = Marca::orderBy('id')->get();
        $token = request()->bearerToken();
        $usuarioValido = Usuario::where('token', '=', $token)->first();
        if ($usuarioValido) {
            return response()->json(['coche' => $coche, 'marcas' => $marcas], 200);
        } else {
            return response()->json(['error' => 'No se proporcionó un token válido'], 401);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Coche  $coche
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coche $coche)
    {
        $token = request()->bearerToken();
        $usuarioValido = Usuario::where('token', '=', $token)->where('es_admin', '=', 1)->first();

        if (!$usuarioValido) {
            return response()->json(['error' => 'No se proporcionó un token válido o no es administrador'], 401);
        }

        if (!$coche) {
            return response()->json(['mensaje' => 'El coche no existe'], 404);
        }

        $request->validate([
            'matricula' => 'required|min:7|max:7|unique:coches,matricula,' . $coche->id . ',id',
            'modelo' => 'required',
            'color' => 'required',
            'motor' => 'required',
        ]);

        $coche->matricula = $request->matricula;
        $coche->modelo = $request->modelo;
        $coche->color = $request->color;
        $coche->motor = $request->motor;

        $coche->marca_id = $coche->marca_id;

        if ($request->hasFile('imagen')) {
            $ruta = $request->file('imagen')->store('/public/imagenes');
            $coche->ruta_img = Storage::url($ruta);

        } else {
            $coche->ruta_img = $coche->ruta_img;
        }

        $coche->save();

        return response()->json(['mensaje' => 'Coche actualizado correctamente']);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Coche  $coche
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coche $coche)
    {
        //
        $token = request()->bearerToken();
        $usuarioValido = Usuario::where('token', '=', $token)->where('es_admin', '=', 1)->first();

        if (!$usuarioValido) {
            return response()->json(['error' => 'No se proporcionó un token válido o no es administrador'], 401);
        }

        if (!$coche) {
            return response()->json(['mensaje' => 'El coche no existe'], 404);
        }

        $coche->delete();

        return response()->json(['mensaje' => 'coche eliminado correctamente']);
    }

    public function comprar($coche)
    {
        $coche = Coche::find($coche);
        //
        $token = request()->bearerToken();
        $usuarioValido = Usuario::where('token', '=', $token)->first();

        if (!$usuarioValido) {
            return response()->json(['error' => 'No se proporcionó un token válido o no es administrador'], 401);
        }

        if (!$coche) {
            # code...
            return response()->json(['mensaje' => 'El coche no existe'], 404);
        }

        $coche->usuario_id = $usuarioValido->id;
        $coche->save();

        return response()->json(['mensaje' => 'Coche comprado']);
    }

    public function vender($coche)
    {
        $coche = Coche::find($coche);
        //
        $token = request()->bearerToken();
        $usuarioValido = Usuario::where('token', '=', $token)->first();

        if (!$usuarioValido) {
            return response()->json(['error' => 'No se proporcionó un token válido o no es administrador'], 401);
        }

        if (!$coche) {
            # code...
            return response()->json(['mensaje' => 'El coche no existe'], 404);
        }

        $coche->usuario_id = null;
        $coche->save();

        return response()->json(['mensaje' => 'Coche vendido']);
    }


    public function filtrarEstado(Request $request)
    {
        try {

            if ($request->usuario_id > 0) {
                # code...
                $coches = Coche::latest()->where('usuario_id', '>=', 0)->get();
            } else {
                $coches = Coche::latest()->where('usuario_id', '=', $request->usuario_id)->get();
            }

            if ($coches) {
                # code...
                return response($coches, 200);
            } else {
                return response()->json(['mensaje' => 'No hay coches del usuario - ' + $request]);
            }
        } catch (\Throwable $th) {
            return response($th, 404);
        }
    }

    public function filtroCompleto(Request $request)
    {
        try {
            if ($request->usuario_id >= 0) {
                # code...
                $marca = Marca::where('nombre', $request->nombre)->first();
                $coches = Coche::latest()->where('usuario_id', '>=', 0)->where('marca_id', $marca->id)->get();
                $marcas = Marca::orderBy('id')->get();
            }

            if (!$request->usuario_id) {
                # code...
                $marca = Marca::where('nombre', $request->nombre)->first();
                $coches = Coche::latest()->where('usuario_id', '=', $request->usuario_id)->where('marca_id', $marca->id)->get();
                $marcas = Marca::orderBy('id')->get();
            }


            $indexArray = [
                'coches' => $coches,
                'marcas' => $marcas
            ];
            return response($indexArray, 200);
        } catch (\Throwable $th) {
            return response($th, 404);
        }
    }
}
