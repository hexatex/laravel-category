<?php

namespace Hexatex\LaravelCategory\Category;

use Hexatex\LaravelCategory\CategoryType\CategoryType;
use Hexatex\LaravelCategory\Category\Builders\BuilderFactory;
use Hexatex\LaravelCategory\Category\Category as ConcreteCategory;
use Hexatex\LaravelCategory\Category\Contracts\Category;
use Hexatex\LaravelCategory\Category\Contracts\CategoryRepository as CategoryRepositoryContract;
use Hexatex\LaravelCategory\Category\Requests\Admin\StoreRequest;
use Hexatex\LaravelCategory\Category\Requests\Admin\UpdateRequest;
use Hexatex\LaravelCategory\Category\Requests\Admin\IndexRequest;
use Hexatex\LaravelMetadata\Metadata\Contracts\Metadata;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CategoryRepository implements CategoryRepositoryContract
{
    public function __construct(protected BuilderFactory $builderFactory)
    {
    }

    /**
     * For filter parameters see IndexRequest
     * 
     * @see IndexRequest
     * @return Category[]|Collection|LengthAwarePaginator
     */
    public function index(array $filters): Collection|LengthAwarePaginator
    {
        return $this->getModel()::filter($filters)
            ->paginate($filters['rowsPerPage'] ?? config('category.default-rows-per-page'));
    }

    /**
     * For dependencies expected in the $fill see StoreRequest
     * 
     * @see StoreRequest
     */
    public function store(array $fill, ?CategoryType $categoryType, Metadata $metadata): Category
    {
        $category = $this->create();
        $builder = $this->builderFactory->create($fill, $category);
        $builder->forStore($categoryType, $metadata);
        $category->save();

        return $category;
    }

    /**
     * For dependencies expected in the $fill see UpdateRequest
     * 
     * @see UpdateRequest
     */
    public function update(array $fill, Category $category): void
    {
        $builder = $this->builderFactory->create($fill, $category);
        $builder->forUpdate();
        $category->save();
    }

    public function destroy(Category $category): void
    {
        $category->delete();
        $category->images()->detach();
    }

    /*
     * Protected Methods
     */
    protected function getModel(): string
    {
        return ConcreteCategory::class;
    }

    protected function create(): Category
    {
        return new ($this->getModel());
    }
}
