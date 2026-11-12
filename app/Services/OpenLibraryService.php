<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenLibraryService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function searchBooks(string $title): array
    {
        $response = Http::timeout(10)->get('https://openlibrary.org/search.json', [
            'q' => $title,
            'fields' => 'key,title,author_name,cover_i,isbn',
            'limit' => 10,
        ])->throw();

        $books = $response->json('docs', []);

        return is_array($books) ? $books : [];
    }

    public function coverUrl(?int $coverId): string
    {
        return $coverId
            ? "https://covers.openlibrary.org/b/id/{$coverId}-M.jpg"
            : '';
    }

    /**
     * Fetch the description of a book from Open Library.
     *
     * @param ?string $workKey The key of the work to fetch the description for.
     * @return array{description: string|null}
     */
    public function fetchDescription(?string $workKey): array
    {
        if ($workKey === null || ! str_starts_with($workKey, '/works/')) {
            return ['description' => null];
        }

        $workResponse = Http::timeout(10)->get("https://openlibrary.org{$workKey}.json")->throw();

        $description = $workResponse->successful()
            ? $this->descriptionFromResponse($workResponse->json('description'))
            : null;

        return ['description' => $description];
    }

    private function descriptionFromResponse(mixed $description): ?string
    {
        if (is_string($description)) {
            return $description;
        }

        if (is_array($description) && is_string($description['value'] ?? null)) {
            return $description['value'];
        }

        return null;
    }
}
