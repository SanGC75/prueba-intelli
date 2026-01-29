<?php

namespace App\Jobs;

use App\Author;
use App\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateAuthorBookCount implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $authorId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($authorId)
    {
        $this->authorId = $authorId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $author = Author::find($this->authorId);
        
        if ($author) {
            $count = Book::where('author_id', $this->authorId)->where('deleted', false)->count();
            
            $author->update(['books_count' => $count]);
        }
    }
}
