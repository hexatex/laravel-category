<?php

namespace Hexatex\LaravelCategory\Tests\Unit\CategoryItem\Resources\Web;

use Hexatex\LaravelCategory\WebCategorizableResources;
use Hexatex\LaravelCategory\CategoryItem\Resources\Web\CategoryItemResource;
use Hexatex\LaravelCategory\Category\Resources\Web\CategoryResource;
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

        $resource = new CategoryItemResource($categoryItem);

        // Act
        $data = $resource->toArray(request());

        // Assert
        $this->assertEquals([
            'category' => new CategoryResource($category),
            'categorizable' => WebCategorizableResources::single($categorizable),
        ], $data);
    }
}
