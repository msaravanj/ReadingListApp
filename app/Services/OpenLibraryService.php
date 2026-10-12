<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenLibraryService
{
    /**
     * @return array<int, array<string, mixed>>
     */
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

        $books = $response->json('docs', []);

        return is_array($books) ? $books : [];
    }

    public function coverUrl(?int $coverId): string
    {
        return $coverId
            ? "https://covers.openlibrary.org/b/id/{$coverId}-M.jpg"
            : '';
    }
}
