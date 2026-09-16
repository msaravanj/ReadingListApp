<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);


         Category::create(['name' => 'Fiction']);
         Category::create(['name' => 'Romance']);
         Category::create(['name' => 'Programming']);
         Category::create(['name' => 'Science']);
         Category::create(['name' => 'History']);

         Book::factory(6)->create();
    
    }
}
