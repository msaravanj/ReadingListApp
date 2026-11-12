<?php

use App\Enums\BookStatus;
use App\Http\Requests\OpenLibraryBookRequest;
use App\Models\Book;
use App\Models\RecommendedBook;
use App\Services\OpenLibraryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

new class extends Component {
    use Interactions;

    #[Url]
    public $search = '';

    public $foundBooks = [];

    public $isLoading = false;

    public $books = [];

    public $recommendedBooks = [];

    #[Url]
    public $filterStatus = '';

    public $currentPage = 1;

    public $booksPerPage = 8;

    public function mount(): void
    {
        $this->loadBooks();
        $this->loadRecommendedBooks();
    }

    private function loadRecommendedBooks(): void
    {
        try {
            $this->recommendedBooks = RecommendedBook::query()->get();
        } catch (QueryException $e) {
            report($e);
            $this->recommendedBooks = [];
            $this->toast()->error('Error while loading recommended books.')->send();
        }
    }

    public function updatedSearch(): void
    {
        $this->fetchBooks();
    }

    public function fetchBooks(): void
    {
        try {
            $this->foundBooks = app(OpenLibraryService::class)->searchBooks($this->search);
        } catch (ConnectionException | RequestException $e) {
            report($e);
            $this->foundBooks = [];
            $this->toast()->error('Error while searching for books.')->send();
        }
    }

    /**
     * @return array{description: string|null, rating: int|null}|null
     */
    public function fetchDescription(?string $workKey): ?array
    {
        try {
            return app(OpenLibraryService::class)->fetchDescription($workKey);
        } catch (ConnectionException | RequestException $e) {
            report($e);
            $this->toast()->error('Error while fetching book details.')->send();
        }

        return null;
    }

    public function addRecommendedBook(int $recommendedBookId): bool
    {
        try {
            $recommendedBook = RecommendedBook::query()->findOrFail($recommendedBookId);
            $bookData = [
                'title' => $recommendedBook->title,
                'author' => $recommendedBook->author,
                'cover_image' => $recommendedBook->cover_image,
                'isbn' => $recommendedBook->isbn,
                'rating' => null,
                'notes' => $recommendedBook->notes,
                'status' => BookStatus::WANT_TO_READ->value,
            ];

            $existingBook = Book::where('title', $bookData['title'])->where('author', $bookData['author'])->first();

            if ($existingBook) {
                $this->toast()->error('This book is already in your reading list!')->send();

                return false;
            }

            DB::transaction(function () use ($recommendedBook, $bookData): void {
                Book::create($bookData);
                $recommendedBook->delete();
            });
            $this->toast()->success('Recommended book added to your reading list!')->send();
        } catch (ModelNotFoundException $e) {
            report($e);
            $this->toast()->error('Recommended book could not be found.')->send();

            return false;
        } catch (QueryException $e) {
            report($e);
            $this->toast()->error('Error while adding the recommended book.')->send();

            return false;
        }

        $this->loadBooks();
        $this->loadRecommendedBooks();

        return true;
    }

    public function addBook(int $bookIndex): bool
    {
        $bookData = $this->foundBooks[$bookIndex] ?? null;
        $saved = false;

        if (!is_array($bookData)) {
            $this->toast()->error('Error occurred while adding the book.')->send();

            return false;
        }

        try {
            $bookData = validator($bookData, new OpenLibraryBookRequest()->rules())->validate();
            $openLibraryService = app(OpenLibraryService::class);
            $descriptionData = $openLibraryService->fetchDescription($bookData['key'] ?? null);

            $title = $bookData['title'] ?? 'Untitled';
            $author = !empty($bookData['author_name']) ? implode(', ', $bookData['author_name']) : 'Unknown Author';
            $book = new Book();
            $book->title = $title;
            $book->author = $author;
            $book->cover_image = $openLibraryService->coverUrl(is_int($bookData['cover_i'] ?? null) ? $bookData['cover_i'] : null);
            $book->isbn = !empty($bookData['isbn']) ? $bookData['isbn'][0] : '';
            $book->rating = null;
            $book->notes = $descriptionData['description'] ?? '';
            $book->status = BookStatus::WANT_TO_READ->value;

            $existingBook = Book::where('title', $title)->where('author', $author)->first();

            if ($existingBook) {
                $this->toast()->error('This book is already in your reading list!')->send();

                return false;
            }

            $book->save();
            $this->toast()->success('Book added to your reading list!')->send();
            $saved = true;
        } catch (ConnectionException | RequestException $e) {
            report($e);
            $this->toast()->error('Error while fetching book details.')->send();
        } catch (ValidationException $e) {
            report($e);
            $this->toast()->error('Invalid book data.')->send();
        } catch (QueryException $e) {
            report($e);
            $this->toast()->error('Error while adding the book.')->send();
        }

        $this->loadBooks();

        return $saved;
    }

    private function loadBooks(): void
    {
        try {
            $this->books = Book::query()->get();
        } catch (QueryException $e) {
            report($e);
            $this->books = [];
            $this->toast()->error('Error while loading your reading list.')->send();
        }
    }

    public function updateBookRating(int $bookId, ?int $rating): bool
    {
        try {
            validator(['rating' => $rating], ['rating' => ['nullable', 'integer', 'between:1,5']])->validate();

            $book = Book::query()->findOrFail($bookId);
            $book->update(['rating' => $rating]);
            $this->loadBooks();

            return true;
        } catch (ModelNotFoundException $e) {
            report($e);
            $this->toast()->error('Book could not be found.')->send();
        } catch (QueryException $e) {
            report($e);
            $this->toast()->error('Error while saving the rating.')->send();
        }

        return false;
    }

    public function updateBookStatus(int $bookId, string $status): bool
    {
        try {
            $validated = validator(['status' => $status], ['status' => ['required', Rule::enum(BookStatus::class)]])->validate();

            $book = Book::query()->findOrFail($bookId);
            $book->update(['status' => $validated['status']]);
            $this->loadBooks();

            return true;
        } catch (ModelNotFoundException $e) {
            report($e);
            $this->toast()->error('Book could not be found.')->send();
        } catch (ValidationException $e) {
            report($e);
            $this->toast()->error('Invalid book status.')->send();
        } catch (QueryException $e) {
            report($e);
            $this->toast()->error('Error while saving the status.')->send();
        }

        return false;
    }

    public function updateBookNotices(int $bookId, ?string $notices): bool
    {
        try {
            $validated = validator(
                ['notices' => $notices],
                [
                    'notices' => ['nullable', 'string', 'max:10000'],
                ],
            )->validate();

            $book = Book::query()->findOrFail($bookId);
            $book->update(['notices' => $validated['notices']]);
            $this->loadBooks();

            return true;
        } catch (ModelNotFoundException $e) {
            report($e);
            $this->toast()->error('Book could not be found.')->send();
        } catch (ValidationException $e) {
            report($e);
            $this->toast()->error('Invalid book notices.')->send();
        } catch (QueryException $e) {
            report($e);
            $this->toast()->error('Error while saving the notices.')->send();
        }

        return false;
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
    <x-recommended-books-section :recommendedBooks="$recommendedBooks" />
    <x-my-books-section :books="$books" />
    <x-api-book-modal />
    <x-recommended-book-modal />
    <x-book-modal />
</div>
