<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenLibraryService
{
    public function __construct(
        private BookDescriptionService $bookDescriptionService
    ) {}

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
     * @param  ?string  $workKey  The key of the work to fetch the description for.
     * @param  ?string  $title  The title of the book.
     * @param  ?string  $author  The author of the book.
     * @return array{description: string|null}
     */
    public function fetchDescription(?string $workKey, ?string $title = null, ?string $author = null): array
    {
        $description = null;

        if ($workKey !== null && str_starts_with($workKey, '/works/')) {
            $workResponse = Http::timeout(10)->get("https://openlibrary.org{$workKey}.json")->throw();
            $description = $workResponse->successful()
                ? $this->descriptionFromResponse($workResponse->json('description'))
                : null;
        }

        if (blank($description) && filled($title) && filled($author)) {
            $description = $this->bookDescriptionService->fillIfEmpty($title, $author, null);
        }

        return ['description' => $description];
    }

    private function descriptionFromResponse(mixed $description): ?string
    {
        if (is_string($description) && $description !== '') {
            return $description;
        }

        if (is_array($description) && is_string($description['value'] ?? null)) {
            return $description['value'];
        }

        return null;
    }
}
