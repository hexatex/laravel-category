<?php

namespace Hexatex\LaravelCategory\CategoryItem;

use Hexatex\LaravelCategory\Category\Contracts\Category;
use Hexatex\LaravelCategory\CategoryItem\Contracts\Categorizable;
use Hexatex\LaravelCategory\CategoryItem\Contracts\CategoryItem;
use Hexatex\LaravelCategory\CategoryItem\Contracts\CategoryItemRepository;
use Hexatex\LaravelCategory\CategoryItem\Contracts\CategoryItemService as CategoryItemServiceContract;
use Hexatex\LaravelCategory\CategoryItem\Requests\Admin\CategoryItemRequest;
use Hexatex\LaravelCategory\CategoryItem\Requests\Admin\IndexRequest;
use Hexatex\LaravelService\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CategoryItemService extends Service implements CategoryItemServiceContract
{
    public function __construct(CategoryItemRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @see IndexRequest
     * @return CategoryItem[]|Collection|LengthAwarePaginator
     */
    public function index(array $filters, Category $category): Collection|LengthAwarePaginator
    {
        return $this->repository->index($filters, $category);
    }

    /**
     * @see CategoryItemRequest
     */
    public function store(array $fill, Categorizable $categorizable): CategoryItem
    {
        return $this->repository->store($fill, $categorizable);
    }

    /**
     * @see CategoryItemRequest
     */
    public function update(array $fill, CategoryItem $categoryItem): void
    {
        $this->repository->update($fill, $categoryItem);
    }

    public function destroy(CategoryItem $categoryItem): void
    {
        $this->repository->destroy($categoryItem);
    }

    public function storeUpdateDestroy(array $fill, Categorizable $categorizable): void
    {
        $this->storeUpdateDestroyMany(
            $fill,
            $categorizable->categoryItems,
            store: fn ($fill) => $this->store($fill, $categorizable),
            update: fn ($fill, $categoryItem) => $this->update($fill, $categoryItem),
            destroy: fn ($categoryItem) => $this->destroy($categoryItem),
        );
    }
}
