<?php

use App\Enums\BookStatus;
use App\Http\Requests\OpenLibraryBookRequest;
use App\Models\Book;
use App\Services\OpenLibraryService;
use Flux\Flux;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component {
    #[Url]
    public $search = '';

    public $foundBooks = [];

    public $isLoading = false;

    public $books = [];

    public $filteredBooks = [];

    public $filterStatus = '';

    public function mount(): void
    {
        $this->books = Book::query()->get();
        $this->filteredBooks = $this->books;
    }

    public function updatedSearch(): void
    {
        $this->fetchBooks();
    }

    public function fetchBooks(): void
    {
        if (strlen($this->search) < 3) {
            $this->foundBooks = [];

            return;
        }

        $this->isLoading = true;
        $this->foundBooks = app(OpenLibraryService::class)->searchByTitle($this->search);

        $this->isLoading = false;
    }

    public function addBook(array $bookData): void
    {
        $bookData = validator($bookData, new OpenLibraryBookRequest()->rules())->validate();
        $openLibraryService = app(OpenLibraryService::class);
        $title = $bookData['title'] ?? 'Untitled';
        $author = !empty($bookData['author_name']) ? implode(', ', $bookData['author_name']) : 'Unknown Author';
        $book = new Book();
        $book->title = $title;
        $book->author = $author;
        $book->cover_image = $openLibraryService->coverUrl($bookData['cover_i'] ?? null);
        $book->isbn = !empty($bookData['isbn']) ? implode(', ', $bookData['isbn']) : '';
        $book->rating = null;
        $book->notes = $bookData['editions_description'] ?? '';
        $book->status = BookStatus::WANT_TO_READ->value;

        try {
            $existingBook = Book::where('title', $title)->where('author', $author)->first();

            if ($existingBook) {
                Flux::toast(variant: 'warning', text: __('This book is already in your reading list.'));

                return;
            }

            $book->save();
            Flux::toast(variant: 'success', text: __('Book added to your reading list!'));
        } catch (\Exception $e) {
            report($e);
            Flux::toast(variant: 'error', text: __('Error while adding the book: '));

            return;
        }

        // Refresh liste
        $this->books = Book::query()->get();
        $this->filterBooks();
    }

    public function updatedFilterStatus(): void
    {
        $this->filterBooks();
    }

    private function filterBooks(): void
    {
        $query = Book::query();

        if ($this->filterStatus !== '') {
            $query->where('status', $this->filterStatus);
        }

        $this->filteredBooks = $query->get();
    }

    public function getStatusMessageProperty(): string
    {
        return match ($this->filterStatus) {
            BookStatus::WANT_TO_READ->value => 'Showing books you want to read.',
            BookStatus::READING->value => 'Showing books you are currently reading.',
            BookStatus::READ->value => 'Showing books you have read.',
            default => 'Showing all books in your reading list.',
        };
    }
};
?>

<div class="container mt-20 flex w-full flex-col items-center justify-center gap-4 px-4">
    <p>Search for books</p>
    <input type="text" id="searchInput"
        class="form-control mb-3 w-full rounded border border-gray-300 bg-white text-gray-800 placeholder:text-gray-500 sm:w-xs"
        wire:model.live.debounce.500ms="search" placeholder="Search for books..." />
    @if ($isLoading == true)
        <p>Loading...</p>
    @endif
    <div class="list-group mb-20 flex flex-col w-full sm:w-xl gap-4">
        @if (!empty($foundBooks))
            @foreach ($foundBooks as $book)
                <div wire:key="found-book-{{ $book['key'] ?? $loop->index }}"
                    class="flex flex-col gap-4 rounded border border-gray-300 p-4 shadow-md transition-transform sm:flex-row sm:justify-between">
                    @if (isset($book['cover_i']))
                        <img src="https://covers.openlibrary.org/b/id/{{ $book['cover_i'] }}-M.jpg"
                            alt="{{ $book['title'] ?? 'Book cover' }}"
                            class="mx-auto h-48 w-32 shrink-0 rounded object-cover shadow-md sm:mx-0">
                    @endif
                    <div class="flex min-w-0 flex-1 flex-col justify-between">
                        <div>
                            <h3 class="wrap-break-words">{{ $book['title'] ?? 'Untitled' }}</h3>
                            <p class="wrap-break-words text-sm italic text-gray-600">
                                {{ !empty($book['author_name']) ? implode(', ', $book['author_name']) : 'Unknown Author' }}
                            </p>
                        </div>
                        <div>
                            <button
                                class="mt-2 w-full rounded bg-mist-800 px-4 py-2 text-white transition-colors hover:bg-mist-600 cursor-pointer sm:w-auto"
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
    <div id="readingList" class="mb-40 flex w-full flex-col items-center justify-between gap-4">
        <select wire:model.live="filterStatus"
            class="form-control mb-4 rounded border w-full border-gray-300 bg-white text-gray-800 placeholder:text-gray-500 sm:w-xs">
            <option value="">All statuses</option>
            @foreach (BookStatus::cases() as $status)
                <option value="{{ $status->value }}">{{ $status->name }}</option>
            @endforeach
        </select>
        <p class="mb-4 text-gray-600">{{ $this->statusMessage }}</p>
        @forelse ($filteredBooks as $book)
            <x-book-card :book="$book" />
        @empty
            <p class="text-gray-600">You have no books in your reading list.</p>
        @endforelse
    </div>
    <x-book-modal />
</div>
