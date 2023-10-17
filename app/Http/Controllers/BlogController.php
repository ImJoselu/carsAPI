<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Usuario;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    //

    public function index()
    {
        //
        $usuarios = Usuario::orderBy('id')->get();
        $blogs = Blog::orderBy('created_at','desc')->get();

        $indexArray = [
            'usuarios' => $usuarios,
            'blog' => $blogs
        ];

        return response($indexArray ,  200);
    }

    public function store(Request $request)
    {

        //
        $token = request()->bearerToken();
        $usuarioValido = Usuario::where('token', '=', $token)->first();

        if (!$usuarioValido) {
            return response()->json(['error' => 'No se proporcionó un token válido o no es administrador'], 401);
        }

        $request->validate([

            'titulo' => 'required|string',
            'mensaje' => 'required|string',
        ]);

        $blog = new Blog();
        $blog->titulo = $request->input("titulo");
        $blog->mensaje = $request->input("mensaje");
        $blog->likes = random_int(1 , 1000);
        $blog->share = random_int(0 , 100);
        $blog->tipo = $request->input("tipo");
        $fechaPubli = date('Y-m-d');
        $blog->publicada = $fechaPubli;
        $blog->usuario_id = $request->input("id");

        $blog->save();

        return response($blog, 200);
    }
}
