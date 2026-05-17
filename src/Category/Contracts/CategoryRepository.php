<?php

namespace Hexatex\LaravelCategory\Category\Contracts;

use Hexatex\LaravelCategory\CategoryType\CategoryType;
use Hexatex\LaravelCategory\Category\Contracts\Category;
use Hexatex\LaravelMetadata\Metadata\Contracts\Metadata;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CategoryRepository
{
    /**
     * @return Category[]|Collection|LengthAwarePaginator
     */
    public function index(array $filters): Collection|LengthAwarePaginator;
    public function store(array $fill, ?CategoryType $categoryType, Metadata $metadata): Category;
    public function update(array $fill, Category $category): void;
    public function destroy(Category $category): void;
}
