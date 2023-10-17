<?php

namespace App\Http\Controllers;

use App\Models\Coche;
use App\Models\Marca;
use App\Models\Usuario;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $coches = Coche::orderBy('marca_id')->get();
        $marcas = Marca::orderBy('id')->get();
        return response()->json(["marcas" => $marcas,  "coches" => $coches]);
    }

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
            'nombre' => 'required|min:3|max:12|unique:marcas,nombre',
        ]);

        $marca = new Marca();
        $marca->nombre = $request->input("nombre");
        $marca->save();

        return response($marca, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function show(Marca $marca)
    {
        //
        $marca = Marca::find($marca);
        return view('marcas.show', ['marcas' => $marca]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Marca $marca)
    {
        //

        $token = request()->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'No se proporcionó un token válido'], 401);
        }

        if (!$marca) {
            return response()->json(['mensaje' => 'La marca no existe'], 404);
        }

        $this->validate($request, [
            'nombre' => 'required|unique:marcas,nombre'
        ]);

        $marca->nombre = $request->input('nombre');
        $marca->save();

        return response()->json(['mensaje' => 'Marca actualizada correctamente', 'marca' => $marca]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function destroy(Marca $marca)
    {
        //
        if (!$marca) {
            return response()->json(['mensaje' => 'La marca no existe'], 404);
        }
        $token = request()->bearerToken();
        $marcaValida = Coche::where('marca_id' , $marca->id)->first();
        $usuarioValido = Usuario::where('token', '=', $token)->where('es_admin', '=', 1)->first();

        if (!$usuarioValido) {
            return response()->json(['error' => 'No se proporcionó un token válido o no es administrador'], 401);
        }
        if ($marcaValida) {
            return response()->json(['error' => 'La marca trae asociada un coche y no se puede eliminar'], 401);
        } else {
            $marca->delete();
        }
        return response()->json(['mensaje' => 'marca eliminado correctamente']);
    }

    public function filtrar(Request $request)
    {
        try {
            $marca = Marca::where('nombre', $request->nombre)->first();

            $coches = Coche::latest()->where('marca_id', $marca->id)->get();

            $marcas = Marca::orderBy('id')->get();

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
