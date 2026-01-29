<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $books = Book::where('deleted', false)->get();
        return response()->json($books, 200);
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
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:100',
            'publication_year' => 'nullable|string|max:100',
            'author_id' => 'required|string',
        ]);

        try {
            $book = Book::create([
                'title' => $request->title,
                'summary' => $request->summary,
                'publication_year' => $request->publication_year,
                'author_id' => $request->author_id,
            ]);

            return response()->json([
                'message' => 'Libro creado con éxito',
                'book' => $book
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo guardar el libro en la base de datos.',
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
            $book = Book::where('id', $id)->where('deleted', 'false')->first();
            if (!$book) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Libro no encontrado'
                ], 404);
            }
            return response()->json($book, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo encontrar el libro en la base de datos.',
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
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:100',
            'publication_year' => 'nullable|string|max:100',
        ]);

        try {

            $book = Book::find($id);

            if (!$book) {
                return response()->json(['message' => 'Libro no encontrado'], 404);
            }

            $book = Book::update([
                'title' => $request->title,
                'summary' => $request->summary,
                'publication_year' => $request->publication_year
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Libro actualizado correctamente',
                'author' => $book
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo actualizar el libro en la base de datos.',
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

            $book = Book::find($id);

            if (!$book) {
                return response()->json(['message' => 'Libro no encontrado'], 404);
            }

            $book->update([
                'deleted' => true
            ]);
            
            dispatch(new \App\Jobs\UpdateAuthorBookCount($book->author_id));

            return response()->json([
                'status' => 'success',
                'message' => 'Libro eliminado correctamente',
                'book' => $book
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo eliminar el libro en la base de datos.',
                'debug' => env('APP_DEBUG') ? $e->getMessage() : 'Consulte los logs'
            ], 500);
        }
    }
}
