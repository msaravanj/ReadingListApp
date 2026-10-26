<?php

use App\Enums\BookStatus;
use App\Http\Requests\OpenLibraryBookRequest;
use App\Models\Book;
use App\Services\OpenLibraryService;
use TallStackUi\Traits\Interactions;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component {
    use Interactions;

    #[Url]
    public $search = '';

    public $foundBooks = [];

    public $isLoading = false;

    public $books = [];

    #[Url]
    public $filterStatus = '';

    public $currentPage = 1;

    public $booksPerPage = 8;

    public function mount(): void
    {
        $this->books = Book::query()->get();
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

        $this->foundBooks = app(OpenLibraryService::class)->searchByTitle($this->search);
    }

    public function addBook(int $bookIndex): void
    {
        $bookData = $this->foundBooks[$bookIndex] ?? null;

        if (! is_array($bookData)) {
            $this->toast()->error('Book result is no longer available.')->send();

            return;
        }

        $bookData = validator($bookData, new OpenLibraryBookRequest()->rules())->validate();
        $openLibraryService = app(OpenLibraryService::class);
        $metadata = $openLibraryService->bookMetadata($bookData['key'] ?? null);
        $title = $bookData['title'] ?? 'Untitled';
        $author = !empty($bookData['author_name']) ? implode(', ', $bookData['author_name']) : 'Unknown Author';
        $book = new Book();
        $book->title = $title;
        $book->author = $author;
        $book->cover_image = $openLibraryService->coverUrl($bookData['cover_i'] ?? null);
        $book->isbn = !empty($bookData['isbn']) ? implode(', ', $bookData['isbn']) : '';
        $book->rating = $metadata['rating'];
        $book->notes = $metadata['description'] ?? $bookData['editions_description'] ?? '';
        $book->status = BookStatus::WANT_TO_READ->value;

        try {
            $existingBook = Book::where('title', $title)->where('author', $author)->first();

            if ($existingBook) {
                $this->toast()->error('This book is already in your reading list!')->send();
                return;
            }

            $book->save();
            $this->toast()->success('Book added to your reading list!')->send();
        } catch (\Exception $e) {
            report($e);
            $this->toast()->error('Error while adding the book')->send();
            return;
        }

        // Refresh liste
        $this->books = Book::query()->get();
    }

    public function goToPage(int $page): void
    {
        $this->currentPage = max(1, min($page, $this->totalPages));
    }

    public function getPaginatedBooksProperty()
    {
        return $this->filteredBooks->slice(($this->currentPage - 1) * $this->booksPerPage, $this->booksPerPage)->values();
    }

    public function getTotalPagesProperty(): int
    {
        return max(1, (int) ceil($this->filteredBooks->count() / $this->booksPerPage));
    }

    public function getFilteredBooksProperty()
    {
        $query = Book::query();

        if ($this->filterStatus !== '') {
            $query->where('status', $this->filterStatus);
        }

        return $query->get();
    }

    public function getStatusMessageProperty(): string
    {
        return match ($this->filterStatus) {
            BookStatus::WANT_TO_READ->value => 'Books I want to read.',
            BookStatus::READING->value => 'Books I am currently reading.',
            BookStatus::READ->value => 'Books I have read.',
            default => 'My books',
        };
    }
};
?>

<div class="container flex w-full flex-col items-center justify-center gap-4 px-4">
    <x-top-section :foundBooks="$foundBooks" :search="$search" />
    <x-my-books-section :books="$books" />
    <x-book-modal />
</div>
