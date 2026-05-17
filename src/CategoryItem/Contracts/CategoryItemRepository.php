<?php

namespace Hexatex\LaravelCategory\CategoryItem\Contracts;

use Hexatex\LaravelCategory\CategoryItem\Contracts\CategoryItem;
use Hexatex\LaravelCategory\Category\Contracts\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CategoryItemRepository
{
    /**
     * @return Category[]|Collection|LengthAwarePaginator
     */
    public function index(array $filters, Category $category): Collection|LengthAwarePaginator;
    public function store(array $fill, Categorizable $categorizable): CategoryItem;
    public function update(array $fill, CategoryItem $categoryItem): void;
    public function destroy(CategoryItem $categoryItem): void;
}
