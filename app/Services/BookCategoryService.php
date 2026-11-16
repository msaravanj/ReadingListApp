<?php

namespace App\Services;

use App\Ai\Agents\BookCategoryAgent;
use App\Models\Category;
use UnexpectedValueException;

class BookCategoryService
{
    public function __construct(private BookCategoryAgent $agent) {}

    /**
     * @return list<int>
     */
    public function categoryIdsFor(
        string $title,
        string $author,
        ?string $description
    ): array {
        $categories = Category::query()->orderBy('name')->get();

        if ($categories->isEmpty()) {
            return [];
        }

        $availableCategories = [];
        foreach ($categories as $category) {
            if (! is_string($category->name)) {
                throw new UnexpectedValueException('A category has an invalid name.');
            }

            $availableCategories[] = $category->name;
        }
        $response = $this->agent->generateCategories(
            $title,
            $author,
            $description,
            $availableCategories
        );
        $categoryIds = [];

        foreach ($response['categories'] as $name) {
            $category = $categories->first(
                fn (Category $category): bool => is_string($category->name)
                    && strcasecmp($category->name, $name) === 0
            );

            if ($category === null) {
                throw new UnexpectedValueException(
                    "The book category agent returned an unknown category: {$name}."
                );
            }

            $categoryIds[] = $category->getKey();
        }

        return array_values(array_unique($categoryIds));
    }
}
