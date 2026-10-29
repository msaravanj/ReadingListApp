<?php

namespace Tests\Feature;

use App\Enums\BookStatus;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class BookAdditionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_adds_a_book_with_metadata_from_open_library(): void
    {
        Http::fake([
            'https://openlibrary.org/works/OL123W.json' => Http::response([
                'description' => ['value' => 'A book description.'],
            ]),
            'https://openlibrary.org/works/OL123W/ratings.json' => Http::response([
                'summary' => ['average' => 4.4],
            ]),
        ]);

        Livewire::test('pages.books')
            ->set('foundBooks', [[
                'key' => '/works/OL123W',
                'title' => 'Test Book',
                'author_name' => ['Test Author'],
                'cover_i' => 123,
                'isbn' => ['9780000000000'],
            ]])
            ->call('addBook', 0);

        $this->assertDatabaseHas('books', [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'status' => BookStatus::WANT_TO_READ->value,
            'rating' => null,
            'notes' => 'A book description.',
            'cover_image' => 'https://covers.openlibrary.org/b/id/123-M.jpg',
            'isbn' => '9780000000000',
        ]);
    }

    public function test_it_does_not_add_a_duplicate_book(): void
    {
        $this->assertDatabaseCount('books', 0);

        $book = Book::factory()->create([
            'title' => 'Existing Book',
            'author' => 'Existing Author',
        ]);

        Livewire::test('pages.books')
            ->set('foundBooks', [[
                'title' => $book->title,
                'author_name' => [$book->author],
            ]])
            ->call('addBook', 0);

        $this->assertDatabaseCount('books', 1);
    }

    public function test_it_does_not_add_a_book_when_metadata_request_fails(): void
    {
        Http::fake([
            'https://openlibrary.org/works/OL123W.json' => Http::response([], 503),
        ]);

        Livewire::test('pages.books')
            ->set('foundBooks', [[
                'key' => '/works/OL123W',
                'title' => 'Unavailable Metadata Book',
                'author_name' => ['Test Author'],
            ]])
            ->call('addBook', 0);

        $this->assertDatabaseMissing('books', [
            'title' => 'Unavailable Metadata Book',
        ]);
    }
}
