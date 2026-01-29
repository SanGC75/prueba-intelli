<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = ['title', 'summary', 'publication_year', 'author_id', 'deleted'];

    public function author()
    {
        return $this->belongsTo('App\Author');
    }
}
