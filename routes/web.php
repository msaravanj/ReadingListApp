<?php

use App\Models\Book;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages/books', ['books' => Book::query()->get()]);
});

Route::get('/books', function () {
    return view('pages/books', ['books' => Book::query()->get()]);
})->name('home');
