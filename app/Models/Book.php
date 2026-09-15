<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'status',
        'rating',
        'notes',
        'cover_image',
        'isbn',
    ];

    public function categories(): belongsToMany
    {
        return $this->belongsToMany(Category::class, 'book_category');
    }
}
