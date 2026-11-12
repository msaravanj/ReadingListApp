@props(['recommendedBooks' => []])

<div class="my-4 w-full">
    <h2 class="my-4">Recommended books</h2>
    <div class="recommended-books-viewport full-width overflow-hidden px-20">
        <div class="recommended-books-track flex w-max-content">
            @foreach ([$recommendedBooks, $recommendedBooks] as $books)
                @foreach ($books as $book)
                    <div class="w-72 shrink-0 px-2">
                        <x-book-card :book="$book" modal-event="open-recommended-book-modal" />
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
</div>
