<?php

use Livewire\Component;

new class extends Component
{
    public $book;
};
?>

<div class="flex flex-col items-center justify-center p-4 border rounded shadow-md bg-red-100">
    <h3 x-text="book.title"></h3>
    <p x-text="book.author"></p>
</div>