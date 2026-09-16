<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'status' => $this->faker->randomElement(['want_to_read', 'currently_reading', 'read']),
            'rating' => $this->faker->optional()->numberBetween(1, 5),
            'notes' => $this->faker->optional()->paragraph(),
            'cover_image' => $this->faker->optional()->imageUrl(),
            'isbn' => $this->faker->optional()->isbn13(),
        ];
    }
}
