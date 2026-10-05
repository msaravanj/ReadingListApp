<x-layout>
    <x-slot name="title">Books</x-slot>
    <div
        class="container mt-40 flex flex-col items-center justify-center gap-4"
        x-data="{ searchQuery: '', filterBooks() {} }"
    >
        <p>Search for books</p>
        <input type="text" id="searchInput" class="form-control mb-3 border border-gray-300 rounded bg-white text-gray-800 placeholder:text-gray-500" x-model="searchQuery" @input="filterBooks">  
        <h1 class="mt-8 mb-2">Your reading list</h1>
        <div class="list-group mb-20 flex flex-col gap-4">
            @foreach ($books as $book)
                <x-book-card :book="$book" />
            @endforeach
        </div>
        <x-book-modal />
    </div>
</x-layout>