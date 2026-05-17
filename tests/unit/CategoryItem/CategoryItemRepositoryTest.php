<?php

namespace Hexatex\LaravelCategory\Tests\Unit\CategoryItem;

use Hexatex\LaravelCategory\CategoryItem\Builders\BuilderFactory;
use Hexatex\LaravelCategory\CategoryItem\CategoryItemRepository;
use Hexatex\LaravelCategory\CategoryItem\Contracts\Categorizable;
use Hexatex\LaravelCategory\CategoryItem\Contracts\CategoryItem;
use Hexatex\LaravelCategory\CategoryItem\CategoryItem as ConcreteCategoryItem;
use Hexatex\LaravelCategory\CategoryItem\Contracts\CategoryItemable;
use Hexatex\LaravelCategory\Category\Contracts\Category;
use Hexatex\LaravelCategory\Factories\CategoryItemFactory;
use Hexatex\LaravelCategory\Tests\TestCase;
use Hexatex\LaravelMisc\Contracts\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator as ConcreteLengthAwarePaginator;
use Illuminate\Support\Collection;
use \Mockery;

class CategoryItemRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $categoryItemRepository;
    protected $builderFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builderFactory = $this->mock(BuilderFactory::class);
        $this->categoryItemRepository = $this->getMockBuilder(CategoryItemRepository::class)
            ->onlyMethods(['getModel', 'create'])
            ->setConstructorArgs([$this->builderFactory])
            ->getMock();
    }

    public function testIndex_hasRowsPerPage()
    {
        $filters = [
            'title' => 'Test Title',
            'descending' => true,
            'page' => 1,
            'rowsPerPage' => 10,
            'sortBy' => 'created_at',
        ];

        $this->testIndex($filters);
    }

    public function testIndex_nullRowsPerPage()
    {
        $filters = [
            'title' => 'Test Title',
            'descending' => true,
            'page' => 1,
            'rowsPerPage' => null,
            'sortBy' => 'created_at',
        ];

        $this->testIndex($filters);
    }

    public function testIndex_noRowsPerPage()
    {
        $filters = [
            'title' => 'Test Title',
            'descending' => true,
            'page' => 1,
            'sortBy' => 'created_at',
        ];

        $this->testIndex($filters);
    }

    public function testStore()
    {
        // Arrange
        $fill = $this->categoryItemFill();
        $categoryItem = $this->mockCategoryItem();
        $builder = $this->mockCategoryItemBuilder();
        $categorizable = Mockery::mock(Categorizable::class);

        // Act
        $this->categoryItemRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn($categoryItem);

        $this->builderFactory
            ->shouldReceive('create')
            ->once()
            ->with($fill, $categoryItem)
            ->andReturn($builder);

        $builder
            ->shouldReceive('forStore')
            ->once()
            ->with($categorizable);

        $categoryItem
            ->shouldReceive('save')
            ->once()
            ->with();

        $result = $this->categoryItemRepository->store($fill, $categorizable);

        // Assert
        $this->assertInstanceOf(CategoryItem::class, $result);
        $this->assertEquals($categoryItem, $result);
    }

    public function testUpdate()
    {
        // Arrange
        $fill = $this->categoryItemFill();
        $categoryItem = $this->mockCategoryItem();
        $builder = $this->mockCategoryItemBuilder();

        // Act
        $this->builderFactory
            ->shouldReceive('create')
            ->once()
            ->with($fill, $categoryItem)
            ->andReturn($builder);

        $builder
            ->shouldReceive('forUpdate')
            ->once()
            ->with();

        $categoryItem
            ->shouldReceive('save')
            ->once();

        $this->categoryItemRepository->update($fill, $categoryItem);

        // Assert
        $this->addToAssertionCount(1);
    }

    public function testDestroy()
    {
        // Arrange
        $categoryItem = $this->mockCategoryItem();

        // Act
        $categoryItem
            ->shouldReceive('delete')
            ->once();

        $this->categoryItemRepository->destroy($categoryItem);

        // Assert
        $this->addToAssertionCount(1);
    }

    public function testGetModel()
    {
        // Arrange
        $repository = new CategoryItemRepository($this->builderFactory);
        $method = $this->getInaccessibleMethod($repository, 'getModel');

        // Act
        $result = $method->invoke($repository);

        // Assert
        $this->assertEquals(ConcreteCategoryItem::class, $result);
    }

    public function testCreate()
    {
        // Arrange
        $repository = $this->getMockBuilder(CategoryItemRepository::class)
            ->onlyMethods(['getModel'])
            ->setConstructorArgs([$this->builderFactory])
            ->getMock();

        $method = $this->getInaccessibleMethod($repository, 'create');

        // Act
        $repository
            ->expects($this->once())
            ->method('getModel')
            ->willReturn(ConcreteCategoryItem::class);

        $result = $method->invoke($repository);

        // Assert
        $this->assertInstanceOf(CategoryItem::class, $result);
    }

    /*
     * Protected Methods
     */
    protected function testIndex(array $filters)
    {
        // Arrange
        $categoryItem = $this->mockCategoryItem();
        $queryBuilder = $this->mock(\Illuminate\Database\Eloquent\Builder::class);
        $expectedCollection = new Collection([$categoryItem]);
        $rowsPerPage = $filters['rowsPerPage'] ?? config('category-item.default-rows-per-page');
        $expectedLengthAwarePaginator = new ConcreteLengthAwarePaginator(
            $expectedCollection,
            $expectedCollection->count(),
            $rowsPerPage,
            $filters['page']
        );
        $category = Mockery::mock(Category::class);

        // Act
        $category
            ->shouldReceive('items')
            ->once()
            ->with()
            ->andReturn($queryBuilder);

        $queryBuilder
            ->shouldReceive('filter')
            ->once()
            ->with($filters)
            ->andReturnSelf();

        $queryBuilder
            ->shouldReceive('paginate')
            ->once()
            ->with($rowsPerPage)
            ->andReturn($expectedLengthAwarePaginator);

        $result = $this->categoryItemRepository->index($filters, $category);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(1, $result);
        $this->assertTrue($result->contains($categoryItem));
        $this->assertEquals($categoryItem, $result->first());
    }

    protected function mockCategoryItem()
    {
        return $this->mock(CategoryItem::class);
    }

    protected function mockCategoryItemBuilder()
    {
        return $this->mock(\Hexatex\LaravelCategory\CategoryItem\Builders\Builder::class);
    }

    protected function categoryItemFill()
    {
        return CategoryItemFactory::new()->make()->toArray();
    }
}
