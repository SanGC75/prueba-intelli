<?php

namespace App\Http\Controllers;

use App\Author;
use App\Book;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportData()
    {
        Excel::create('Reporte_Biblioteca', function($excel) {

            $excel->sheet('Autores', function($sheet) {
                $authors = Author::all(['id', 'name', 'nationality', 'books_count', 'created_at']);
                $sheet->fromArray($authors);
            });

            $excel->sheet('Libros', function($sheet) {
                $books = Book::where('deleted', false)
                             ->get(['id', 'title', 'author_id', 'created_at']);
                $sheet->fromArray($books);
            });

        })->export('xls');
    }
}