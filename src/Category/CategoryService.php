<?php

namespace Hexatex\LaravelCategory\Category;

use Hexatex\LaravelCategory\CategoryType\TypeServiceFactory;
use Hexatex\LaravelCategory\Category\Contracts\Category;
use Hexatex\LaravelCategory\Category\Contracts\CategoryRepository;
use Hexatex\LaravelCategory\Category\Contracts\CategoryService as CategoryServiceContract;
use Hexatex\LaravelCategory\Category\Requests\Admin\StoreRequest;
use Hexatex\LaravelCategory\Category\Requests\Admin\UpdateRequest;
use Hexatex\LaravelCategory\Category\Requests\Admin\IndexRequest;
use Hexatex\LaravelMetadata\Metadata\Contracts\MetadataService;
use Hexatex\LaravelService\Service;
use Hexatex\LaravelSlug\Slug\Contracts\SlugService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CategoryService extends Service implements CategoryServiceContract
{
    public function __construct(
        CategoryRepository $repository,
        protected TypeServiceFactory $typeServiceFactory,
        protected SlugService $slugService,
        protected MetadataService $metadataService,
    ) {
        $this->repository = $repository;
    }

    /**
     * For filter parameters see IndexRequest
     * 
     * @see IndexRequest
     * @return Category[]|Collection|LengthAwarePaginator
     */
    public function index(array $filters): Collection|LengthAwarePaginator
    {
        return $this->repository->index($filters);
    }

    /**
     * For dependencies expected in the $fill see StoreRequest
     * 
     * @see StoreRequest
     */
    public function store(array $fill): Category
    {
        $typeService = $this->typeServiceFactory->create($fill['type']['morph_class'] ?? null);
        
        $categoryType = $typeService->store($fill['type'] ?? []);

        $metadata = $this->metadataService->store($fill['metadata'] ?? []);

        $category = $this->repository->store($fill, $categoryType, $metadata);

        $this->slugService->store($fill['slug'] ?? [], $category);

        return $category;
    }

    /**
     * For dependencies expected in the $fill see UpdateRequest
     * 
     * @see UpdateRequest
     */
    public function update(array $fill, Category $category): void
    {
        $typeService = $this->typeServiceFactory->create($category->type_type);
        
        $typeService->update($fill['type'] ?? [], $category->type);

        $this->metadataService->update($fill['metadata'] ?? [], $category->metadata);

        $this->repository->update($fill, $category);

        $category->slug && $this->slugService->update($fill['slug'] ?? [], $category->slug);
    }

    public function destroy(Category $category): void
    {
        if ($category->type) {
            $typeService = $this->typeServiceFactory->create($category->type_type);
            $typeService->destroy($category->type);
        }

        $this->repository->destroy($category);

        $this->metadataService->destroy($category->metadata);

        $category->slug && $this->slugService->destroy($category->slug);
    }
}
