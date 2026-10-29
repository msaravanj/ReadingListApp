@props(['books'])

<div x-data="{
    books: @js(
    $books->map(
        fn($book) => [
            'id' => $book->id,
            'status' => $book->status->value,
            'title' => $book->title,
            'author' => $book->author,
        ],
    ),
),
    filterStatus: '',
    search: '',
    currentPage: 1,
    perPage: 8,
    get filteredBooks() {
        const search = this.search.trim().toLowerCase();

        return this.books.filter(book => {
            const matchesStatus = !this.filterStatus || book.status === this.filterStatus;
            const matchesSearch = !search ||
                book.title.toLowerCase().includes(search) ||
                book.author.toLowerCase().includes(search);

            return matchesStatus && matchesSearch;
        });
    },
    get totalPages() {
        return Math.max(1, Math.ceil(this.filteredBooks.length / this.perPage));
    },
    get visibleBookIds() {
        const start = (this.currentPage - 1) * this.perPage;
        return this.filteredBooks.slice(start, start + this.perPage).map(book => book.id);
    },
    setFilter(status) {
        this.filterStatus = status;
        this.currentPage = 1;
    },
    setSearch(search) {
        this.search = search;
        this.currentPage = 1;
    },
    goToPage(page) {
        this.currentPage = Math.max(1, Math.min(page, this.totalPages));
    }
}" class="mb-12 w-full min-w-0 px-4">
    <hr class="border-t border-library-border my-8" />
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row gap-2 items-center justify-between">
            <div class="flex gap-2 items-center">
                <h2 class="text-2xl font-semibold"
                    x-text="filterStatus ? {
                    want_to_read: 'Books I want to read.',
                    reading: 'Books I am reading.',
                    read: 'Books I have read.'
                }[filterStatus] : 'My books'">
                </h2>
                <x-badge x-text="filteredBooks.length" round sm />
            </div>
            <div class="flex flex-wrap items-center justify-end gap-2">
                <span class="text-sm">Filter by status:</span>
                @foreach ([
        '' => 'All',
        \App\Enums\BookStatus::WANT_TO_READ->value => 'Want to Read',
        \App\Enums\BookStatus::READING->value => 'Reading',
        \App\Enums\BookStatus::READ->value => 'Read',
    ] as $status => $label)
                    <button type="button" x-on:click="setFilter('{{ $status }}')"
                        x-bind:class="filterStatus === '{{ $status }}'
                            ?
                            'bg-gray-700 text-white' :
                            'text-gray-400 hover:bg-gray-800 hover:text-white'"
                        class="rounded border border-library-border px-2 py-1 text-sm transition-colors
                            ">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
        <div class="flex w-full justify-end">
            <label class="sr-only" for="saved-books-search">Search saved books</label>
            <input id="saved-books-search" type="search" x-on:input="setSearch($event.target.value)"
                placeholder="Search saved books..."
                class="w-full rounded border border-library-border bg-library-surface px-3 py-2 text-sm text-white placeholder:text-gray-500 focus:border-gray-500 focus:outline-none sm:max-w-xs">
        </div>

        @if ($books->isEmpty())
            <p class="text-gray-400">No books found.</p>
        @else
            <div class="grid min-w-0 items-stretch grid-cols-2 gap-4 lg:grid-cols-4">
                @foreach ($books as $book)
                    <div wire:key="book-card-{{ $book->id }}" class="h-full min-w-0"
                        x-show="visibleBookIds.includes({{ $book->id }})" x-cloak>
                        <x-book-card :book="$book" />
                    </div>
                @endforeach
            </div>
            <div x-show="totalPages > 1" x-cloak class="mt-6 flex items-center justify-center gap-2">
                <button type="button" x-on:click="goToPage(currentPage - 1)" x-bind:disabled="currentPage === 1"
                    class="rounded border border-library-border px-3 py-1 text-sm disabled:cursor-not-allowed disabled:opacity-50">
                    Previous
                </button>
                <span class="text-sm text-gray-400">
                    Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                </span>
                <button type="button" x-on:click="goToPage(currentPage + 1)"
                    x-bind:disabled="currentPage === totalPages"
                    class="rounded border border-library-border px-3 py-1 text-sm disabled:cursor-not-allowed disabled:opacity-50">
                    Next
                </button>
            </div>
        @endif
    </div>
</div>
