<?php

namespace App\Console\Commands;

use App\Ai\Agents\BookCuratorAgent;
use App\Enums\BookStatus;
use App\Models\RecommendedBook;
use App\Services\OpenLibraryService;
use Illuminate\Console\Command;

class FetchNewBooks extends Command
{
    protected $signature = 'books:fetch-recommended';

    protected $description = 'Find new books by using the Laravel AI SDK and fetch their details from Open Library API.';

    /**
     * Execute the console command.
     */
    public function handle(BookCuratorAgent $agent, OpenLibraryService $openLibraryService)
    {
        $this->info('Pokrećem AI agenta za pretragu knjiga...');

        RecommendedBook::query()->delete();

        // 1. Pozivamo agenta koji odmah vraća formatirani PHP niz
        $response = $agent->findNewReleases();
        $booksData = $response['books'] ?? [];

        foreach ($booksData as $book) {
            $olBooks = $openLibraryService->searchBooks($book['title']);

            if ($olBooks !== []) {
                $olBook = $olBooks[0];
                $coverImage = $openLibraryService->coverUrl(
                    is_int($olBook['cover_i'] ?? null) ? $olBook['cover_i'] : null
                );

                // Spremanje ili ažuriranje u bazi podataka
                RecommendedBook::updateOrCreate(
                    ['open_library_key' => $olBook['key']],
                    [
                        'title' => $olBook['title'],
                        'author' => implode(', ', $olBook['author_name'] ?? [$book['author']]),
                        'notes' => $openLibraryService->fetchBookDescription($olBook['key'])['description'],
                        'status' => BookStatus::WANT_TO_READ,
                        'cover_image' => $coverImage !== '' ? $coverImage : null,
                        'isbn' => $olBook['isbn'][0] ?? null,
                    ]
                );

                $this->line("Book added: {$olBook['title']}");
            }
        }

        $this->info('All books have been successfully synchronized with the Laravel AI!');
    }
}
