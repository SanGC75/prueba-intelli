<?php

namespace App\Observers;

use App\Book;
use App\Jobs\UpdateAuthorBookCount;

class BookObserver
{
    public function created(Book $book)
    {
        dispatch(new UpdateAuthorBookCount($book->author_id));
    }

	public function deleted(Book $book)
    {
        dispatch(new UpdateAuthorBookCount($book->author_id));
    }
}