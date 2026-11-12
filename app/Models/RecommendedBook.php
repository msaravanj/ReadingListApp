<?php

namespace App\Models;

use App\Enums\BookStatus;
use Illuminate\Database\Eloquent\Model;

class RecommendedBook extends Model
{
    protected $fillable = [
        'title',
        'author',
        'status',
        'rating',
        'notes',
        'notices',
        'cover_image',
        'isbn',
        'open_library_key',
    ];

    protected function casts(): array
    {
        return [
            'status' => BookStatus::class,
        ];
    }
}
