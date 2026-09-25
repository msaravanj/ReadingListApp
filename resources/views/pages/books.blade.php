<x-layout>
    <x-slot name="title">Books</x-slot>

    <div class="container mt-40 flex flex-col items-center justify-center gap-4">

        <p>Search for books</p>
        <input type="text" id="searchInput" class="form-control mb-3 border border-gray-300 rounded px-3 py-2" x-model="searchQuery" @input="filterBooks">  
        <h1 class="mt-4 mb-3">Reading list</h1>
        <div class="list-group" x-for="book in books" :key="book.id">
            <book-card :book="book"></book-card>
        </div>
    </div>
</x-layout>