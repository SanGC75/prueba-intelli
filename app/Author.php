<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = ['name', 'nationality', 'books_count', 'deleted'];

    public function books()
    {
        return $this->hasMany('App\Book');
    }
}
