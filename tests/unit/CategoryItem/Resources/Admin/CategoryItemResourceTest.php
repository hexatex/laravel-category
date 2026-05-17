<?php

namespace Hexatex\LaravelCategory\Tests\Unit\CategoryItem\Resources\Admin;

use Hexatex\LaravelCategory\AdminCategorizableResources;
use Hexatex\LaravelCategory\CategoryItem\Resources\Admin\CategoryItemResource;
use Hexatex\LaravelCategory\Category\Resources\Admin\CategoryResource;
use Hexatex\LaravelCategory\Factories\CategoryFactory;
use Hexatex\LaravelCategory\Factories\CategoryItemFactory;
use Hexatex\LaravelCategory\Factories\TestCategorizableFactory;
use Hexatex\LaravelCategory\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class CategoryItemResourceTest extends TestCase
{
    use RefreshDatabase;

    public function testCategoryItemResource()
    {
        // Arrange
        $category = CategoryFactory::new()->make();
        $category->id = 12345;

        $categorizable = TestCategorizableFactory::new()->make();
        $categorizable->id = 12345;

        $categoryItem = CategoryItemFactory::new()->category($category)->categorizable($categorizable)->make();
        $categoryItem->id = 12345;
        $categoryItem->created_at = Carbon::yesterday();
        $categoryItem->updated_at = Carbon::now();

        $resource = new CategoryItemResource($categoryItem);

        // Act
        $data = $resource->toArray(request());

        // Assert
        $this->assertEquals([
            'id' => 12345,
            'created_at' => $categoryItem->created_at,
            'updated_at' => $categoryItem->updated_at,

            'category' => new CategoryResource($category),
            'categorizable' => AdminCategorizableResources::single($categorizable),
        ], $data);
    }
}
