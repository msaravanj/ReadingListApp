<?php

use Livewire\Component;
use App\Models\Book;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Url;
use App\Enums\BookStatus;
use Flux\Flux;

new class extends Component {
    #[Url]
    public $search = '';
    public $foundBooks = [];
    public $isLoading = false;
    public $books = [];

    public function mount(): void
    {
        $this->books = Book::query()->get();
    }

    public function updatedSearch(): void
    {
        $this->fetchBooks();
    }

    public function fetchBooks()
    {
        if (strlen($this->search) < 3) {
            $this->foundBooks = [];
            return;
        }

        $this->isLoading = true;
        $response = Http::get('https://openlibrary.org/search.json', [
            'title' => $this->search,
            'fields' => 'key,title,author_name,cover_i,isbn,editions,editions_description',
            'limit' => 6,
        ]);

        if ($response->successful()) {
            $this->foundBooks = $response->json('docs', []);
        } else {
            $this->foundBooks = [];
        }

        $this->isLoading = false;
        return $this->foundBooks;
    }

    public function addBook($bookData)
    {
        $book = new Book();
        $book->title = $bookData['title'] ?? 'Untitled';
        $book->author = !empty($bookData['author_name']) ? implode(', ', $bookData['author_name']) : 'Unknown Author';
        $book->cover_image = !empty($bookData['cover_i']) ? "https://covers.openlibrary.org/b/id/{$bookData['cover_i']}-M.jpg" : '';
        $book->isbn = !empty($bookData['isbn']) ? implode(', ', $bookData['isbn']) : '';
        $book->rating = null;
        $book->notes = $bookData['editions_description'] ?? '';
        $book->status = BookStatus::WANT_TO_READ->value;

        try {
            $existingBook = Book::where('title', $book->title)->where('author', $book->author)->first();

            if ($existingBook) {
                Flux::toast(variant: 'warning', text: __('This book is already in your reading list.'));
                return;
            }

            $book->save();
            Flux::toast(variant: 'success', text: __('Book added to your reading list!'));
        } catch (\Exception $e) {
            Flux::toast(variant: 'error', text: __('Error while adding the book: '));
            return;
        }

        // Refresh liste
        $this->books = Book::query()->get();
    }
};
?>

<div class="container mt-40 flex flex-col items-center justify-center gap-4">
    <p>Search for books</p>
    <input type="text" id="searchInput"
        class="form-control mb-3 border border-gray-300 rounded bg-white text-gray-800 placeholder:text-gray-500"
        wire:model.live.debounce.500ms="search" placeholder="Search for books..." />
    @if ($isLoading == true)
        <p>Loading...</p>
    @endif
    <div class="list-group flex flex-col mb-20 gap-4">
        @if (!empty($foundBooks))
            @foreach ($foundBooks as $book)
                <div wire:key="found-book-{{ $book['key'] ?? $loop->index }}"
                    class="flex gap-4 justify-between p-4 border rounded shadow-md  border-gray-300 hover:border-gray-600 transition-transform">
                    @if (isset($book['cover_i']))
                        <img src="https://covers.openlibrary.org/b/id/{{ $book['cover_i'] }}-M.jpg"
                            alt="{{ $book['title'] ?? 'Book cover' }}" class="rounded shadow-md w-32 h-48 object-cover">
                    @endif
                    <div class="flex flex-col justify-between">
                        <div>
                            <h3>{{ $book['title'] ?? 'Untitled' }}</h3>
                            <p class="text-gray-600 text-sm italic">
                                {{ !empty($book['author_name']) ? implode(', ', $book['author_name']) : 'Unknown Author' }}
                            </p>
                        </div>
                        <div>
                            <button
                                class="mt-2 px-4 py-2 text-white bg-mist-800 rounded hover:bg-mist-600 cursor-pointer transition-colors"
                                wire:click="addBook({{ json_encode($book) }})">
                                Add to reading list
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <hr class="w-full border-gray-300 my-8">
    <h1 class="mt-8 mb-2">Your reading list</h1>
    <div class="list-group mb-20 flex flex-col gap-4">
        @foreach ($books as $book)
            <x-book-card :book="$book" />
        @endforeach
    </div>
    <x-book-modal />
</div>
