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
        $response = Http::timeout(10)->get('https://openlibrary.org/search.json', [
            'title' => $title,
            'fields' => 'key,title,author_name,cover_i,isbn,editions,editions_description',
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
     * @return array{description: string|null, rating: int|null}
     */
    public function bookMetadata(?string $workKey): array
    {
        if ($workKey === null || ! str_starts_with($workKey, '/works/')) {
            return ['description' => null, 'rating' => null];
        }

        $workResponse = Http::timeout(10)->get("https://openlibrary.org{$workKey}.json")->throw();
        $ratingsResponse = Http::timeout(10)->get("https://openlibrary.org{$workKey}/ratings.json")->throw();

        $description = $workResponse->successful()
            ? $this->descriptionFromResponse($workResponse->json('description'))
            : null;

        $average = $ratingsResponse->successful()
            ? $ratingsResponse->json('summary.average')
            : null;

        return [
            'description' => $description,
            'rating' => is_numeric($average) ? (int) round((float) $average) : null,
        ];
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
