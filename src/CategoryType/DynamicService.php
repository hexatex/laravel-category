<?php

namespace Hexatex\LaravelCategory\CategoryType;

use Hexatex\LaravelCategory\Jobs\Category\SyncDynamicItems;
use Hexatex\LaravelCategory\Category;
use Hexatex\LaravelCategory\CategoryType\Dynamic;
use Hexatex\LaravelCategory\CategoryService;
use Hexatex\LaravelCriteria\CriteriaService;
use Hexatex\LaravelCategory\CategoryItemService;

class DynamicService implements CategoryTypeService
{
    public function __construct(
        private CategoryService $categoryService,
        private CriteriaService $criteriaService,
        private CategoryItemService $categoryItemService,
    ) {
    }

    public function store(array $fill): Dynamic
    {
        $dynamic = new Dynamic;
        $dynamic->save();

        $this->criteriaService->store($fill['category_type']['criteria'], $dynamic);

        dispatch(new SyncDynamicItems($dynamic));

        return $dynamic;
    }

    public function update(array $fill, Dynamic $dynamic): void
    {
        $this->criteriaService->update($fill['category_type']['criteria'], $dynamic->criteria);

        dispatch(new SyncDynamicItems($dynamic));
    }

    /**
     * Destroy
     * @throws \Exception
     */
    public function destroy(Dynamic $dynamic): void
    {
        $dynamic->delete();
    }

    /**
     * Sync CategoryItems by Criteria
     */
    public function syncCategoryItems(Dynamic $dynamic): void
    {
        $dynamic->category->categoryItems()->detach();

        $categoryItems = $this->categoryItemService->indexByCriteria([], $dynamic->criteria, false);

        $this->categoryService->addCategoryItems($categoryItems->toArray(), $dynamic->category);
    }
}
