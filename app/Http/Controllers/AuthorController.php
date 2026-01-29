<?php

namespace App\Http\Controllers;

use App\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $author = Author::where('deleted', false)->paginate(10);
        return response()->json($author, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'nationality' => 'nullable|string|max:100',
        ]);

        try {
            $author = Author::create([
                'name' => $request->name,
                'nationality' => $request->nationality,
                'books_count' => 0
            ]);

            return response()->json([
                'message' => 'Autor creado con éxito',
                'author' => $author
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo guardar el autor en la base de datos.',
                'debug' => env('APP_DEBUG') ? $e->getMessage() : 'Consulte los logs'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $author = Author::where('id', $id)->where('deleted', 'false')->first();
            if (!$author) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Autor no encontrado'
                ], 404);
            }
            return response()->json($author, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo encontrar el autor en la base de datos.',
                'debug' => env('APP_DEBUG') ? $e->getMessage() : 'Consulte los logs'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'nationality' => 'nullable|string|max:100',
        ]);

        try {

            $author = Author::find($id);

            if (!$author) {
                return response()->json(['message' => 'Autor no encontrado'], 404);
            }

            $author = Author::update([
                'name' => $request->name,
                'nationality' => $request->nationality
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Autor actualizado correctamente',
                'author' => $author
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo actualizar el autor en la base de datos.',
                'debug' => env('APP_DEBUG') ? $e->getMessage() : 'Consulte los logs'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $author = Author::find($id);

            if (!$author) {
                return response()->json(['message' => 'Autor no encontrado'], 404);
            }

            $author->update([
                'deleted' => true
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Autor eliminado correctamente',
                'author' => $author
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo eliminar el autor en la base de datos.',
                'debug' => env('APP_DEBUG') ? $e->getMessage() : 'Consulte los logs'
            ], 500);
        }
    }
}
