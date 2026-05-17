<?php

namespace Hexatex\LaravelCategory\Factories;

use Hexatex\LaravelCategory\CategoryItem\CategoryItem;
use Hexatex\LaravelCategory\CategoryItem\Contracts\Categorizable;
use Hexatex\LaravelCategory\Category\Contracts\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryItemFactory extends Factory
{
    protected $model = CategoryItem::class;

    public function definition()
    {
        return [];
    }

    public function category(Category $category)
    {
        return $this->afterMaking(function (CategoryItem $categoryItem) use ($category) {
            $categoryItem->category()->associate($category);
        });
    }

    public function categorizable(Categorizable $categorizable)
    {
        return $this->afterMaking(function (CategoryItem $categoryItem) use ($categorizable) {
            $categoryItem->categorizable()->associate($categorizable);
        });
    }
}
