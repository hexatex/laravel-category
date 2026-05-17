<?php

namespace Hexatex\LaravelCategory\Tests\Unit\CategoryItem;

use \Mockery;
use Hexatex\LaravelCategory\CategoryItem\CategoryItemService;
use Hexatex\LaravelCategory\CategoryItem\Contracts\Categorizable;
use Hexatex\LaravelCategory\Category\Contracts\Category;
use Hexatex\LaravelCategory\CategoryItem\Contracts\CategoryItem;
use Hexatex\LaravelCategory\CategoryItem\Contracts\CategoryItemRepository;
use Hexatex\LaravelCategory\Tests\TestCase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CategoryItemServiceTest extends TestCase
{
    protected $categoryItemRepository;
    protected $categoryItemService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryItemRepository = $this->mock(CategoryItemRepository::class);

        $this->categoryItemService = new CategoryItemService($this->categoryItemRepository);
    }

    public function testIndex()
    {
        // Arrange
        $filters = [
            'descending' => true,
            'page' => 1,
            'rowsPerPage' => 10,
            'sortBy' => 'created_at',
        ];

        $categoryItem = $this->mockCategoryItem();
        $collection = new Collection([$categoryItem]);

        $category = Mockery::mock(Category::class);

        // Act
        $this->categoryItemRepository
            ->shouldReceive('index')
            ->once()
            ->with($filters, $category)
            ->andReturn($collection);

        $result = $this->categoryItemService->index($filters, $category);

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertTrue($result->contains($categoryItem));
        $this->assertEquals($categoryItem, $result->first());
    }

    public function testStore()
    {
        // Arrange
        $fill = [
        ];
        $categoryItem = $this->mockCategoryItem();
        $categorizable = Mockery::mock(Categorizable::class);

        // Act
        $this->categoryItemRepository
            ->shouldReceive('store')
            ->once()
            ->with($fill, $categorizable)
            ->andReturn($categoryItem);

        $result = $this->categoryItemService->store($fill, $categorizable);

        // Assert
        $this->assertInstanceOf(CategoryItem::class, $result);
        $this->assertEquals($categoryItem, $result);
    }

    public function testUpdate()
    {
        // Arrange
        $fill = [
        ];
        $categoryItem = $this->mockCategoryItem();

        // Act
        $this->categoryItemRepository
            ->shouldReceive('update')
            ->once()
            ->with($fill, $categoryItem);

        $this->categoryItemService->update($fill, $categoryItem);

        // Assert
        $this->addToAssertionCount(1);
    }

    public function testDestroy()
    {
        // Arrange
        $categoryItem = $this->mockCategoryItem();

        $this->categoryItemRepository
            ->shouldReceive('destroy')
            ->once()
            ->with($categoryItem);

        // Act
        $this->categoryItemService->destroy($categoryItem);

        // Assert
        $this->addToAssertionCount(1);
    }

    protected function mockCategoryItem()
    {
        return Mockery::mock(CategoryItem::class);
    }
}
