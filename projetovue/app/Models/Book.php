<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'author_id',
        'publisher_id',
        'isbn',
        'publication_year',
        'pages',
        'language'
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'pages' => 'integer',
        'author_id' => 'integer',
        'publisher_id' => 'integer'
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }
}
