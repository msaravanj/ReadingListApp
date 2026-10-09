<?php

namespace App\Models;

use App\Enums\BookStatus;
use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    /**
     * @use HasFactory<BookFactory>
     */
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'status',
        'rating',
        'notes',
        'cover_image',
        'isbn',
    ];

    protected function casts(): array
    {
        return [
            'status' => BookStatus::class,
        ];
    }

    /**
     * @return BelongsToMany<Category, $this>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'book_category');
    }
}
