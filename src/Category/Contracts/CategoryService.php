<?php

namespace Hexatex\LaravelCategory\Category\Contracts;

use Hexatex\LaravelCategory\Category\Contracts\Category;
use Hexatex\LaravelService\Contracts\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CategoryService extends Service
{
    /**
     * @return Category[]|Collection|LengthAwarePaginator
     */
    public function index(array $filters): Collection|LengthAwarePaginator;
    public function store(array $fill): Category;
    public function update(array $fill, Category $category): void;
    public function destroy(Category $category): void;
}
