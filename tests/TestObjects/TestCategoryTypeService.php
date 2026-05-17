<?php

namespace Hexatex\LaravelCategory\Tests\TestObjects;

use Hexatex\LaravelCategory\CategoryType\CategoryType;
use Hexatex\LaravelCategory\CategoryType\CategoryTypeService;
use Hexatex\LaravelService\Service;

class TestCategoryTypeService extends Service implements CategoryTypeService
{
    public function store(array $fill): ?CategoryType
    {
        $categoryType = new TestCategoryType($fill);
        $categoryType->save();

        return $categoryType;
    }

    public function update(array $fill, ?CategoryType $categoryType): void
    {
        $categoryType->fill($fill);
        $categoryType->save();
    }

    public function destroy(?CategoryType $categoryType): void
    {
        $categoryType->delete();
    }
}
