<?php

namespace Hexatex\LaravelCategory\CategoryItem;

use Hexatex\LaravelCategory\CategoryItem\Builders\BuilderFactory;
use Hexatex\LaravelCategory\CategoryItem\CategoryItem as ConcreteCategoryItem;
use Hexatex\LaravelCategory\CategoryItem\Contracts\CategoryItemRepository as CategoryItemRepositoryContract;
use Hexatex\LaravelCategory\CategoryItem\Contracts\Categorizable;
use Hexatex\LaravelCategory\CategoryItem\Contracts\CategoryItem;
use Hexatex\LaravelCategory\CategoryItem\Requests\Admin\CategoryItemRequest;
use Hexatex\LaravelCategory\CategoryItem\Requests\Admin\IndexRequest;
use Hexatex\LaravelCategory\Category\Contracts\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CategoryItemRepository implements CategoryItemRepositoryContract
{
    protected $builderFactory;

    public function __construct(BuilderFactory $builderFactory)
    {
        $this->builderFactory = $builderFactory;
    }

    /**
     * @see IndexRequest
     * @return CategoryItem[]|Collection|LengthAwarePaginator
     */
    public function index(array $filters, Category $category): Collection|LengthAwarePaginator
    {
        return $category->items()
            ->filter($filters)
            ->paginate($filters['rowsPerPage'] ?? config('category-item.default-rows-per-page'));
    }

    /**
     * @see CategoryItemRequest
     */
    public function store(array $fill, Categorizable $categorizable): CategoryItem
    {
        $categoryItem = $this->create();
        $builder = $this->builderFactory->create($fill, $categoryItem);
        $builder->forStore($categorizable);
        $categoryItem->save();

        return $categoryItem;
    }

    /**
     * @see CategoryItemRequest
     */
    public function update(array $fill, CategoryItem $categoryItem): void
    {
        $builder = $this->builderFactory->create($fill, $categoryItem);
        $builder->forUpdate();
        $categoryItem->save();
    }

    public function destroy(CategoryItem $categoryItem): void
    {
        $categoryItem->delete();
    }

    /*
     * Protected Methods
     */
    protected function getModel(): string
    {
        return ConcreteCategoryItem::class;
    }

    protected function create(): CategoryItem
    {
        return new ($this->getModel());
    }
}
