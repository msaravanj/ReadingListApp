<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenLibraryService
{
    public function searchByTitle(string $title): array
    {
        $response = Http::get('https://openlibrary.org/search.json', [
            'title' => $title,
            'fields' => 'key,title,author_name,cover_i,isbn,editions,editions_description',
            'limit' => 6,
        ]);

        if (! $response->successful()) {
            return [];
        }

        return $response->json('docs', []);
    }

    public function coverUrl(?int $coverId): string
    {
        return $coverId
            ? "https://covers.openlibrary.org/b/id/{$coverId}-M.jpg"
            : '';
    }
}
