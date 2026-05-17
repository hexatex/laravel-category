<?php

namespace Hexatex\LaravelCategory\Tests\Unit\Category;

use Hexatex\LaravelCategory\CategoryType\CategoryType;
use Hexatex\LaravelCategory\Category\Builders\BuilderFactory;
use Hexatex\LaravelCategory\Category\Category as ConcreteCategory;
use Hexatex\LaravelCategory\Category\CategoryRepository;
use Hexatex\LaravelCategory\Category\Contracts\Category;
use Hexatex\LaravelCategory\Category\Dtos\CategoryPathDto;
use Hexatex\LaravelCategory\Factories\CategoryFactory;
use Hexatex\LaravelCategory\Tests\TestCase;
use Hexatex\LaravelMetadata\Factories\MetadataFactory;
use Hexatex\LaravelMetadata\Metadata\Contracts\Metadata;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator as ConcreteLengthAwarePaginator;
use Illuminate\Support\Collection;
use \Mockery;

class CategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $categoryRepository;
    protected $builderFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builderFactory = $this->mock(BuilderFactory::class);
        $this->categoryRepository = $this->getMockBuilder(CategoryRepository::class)
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

    public function testStore_withCategoryType()
    {
        $categoryType = $this->mockCategoryType();
        $this->testStore($categoryType);
    }

    public function testStore_withoutCategoryType()
    {
        $this->testStore(null);
    }

    public function testUpdate()
    {
        // Arrange
        $fill = $this->categoryFillData();
        $category = $this->mockCategory();
        $builder = $this->mockCategoryBuilder();

        // Act
        $this->builderFactory
            ->shouldReceive('create')
            ->once()
            ->with($fill, $category)
            ->andReturn($builder);

        $builder
            ->shouldReceive('forUpdate')
            ->once()
            ->with();

        $category
            ->shouldReceive('save')
            ->once();

        $result = $this->categoryRepository->update($fill, $category);

        // Assert
        $this->assertNull($result);
    }

    public function testDestroy()
    {
        // Arrange
        $category = $this->mockCategory();
        $imagesMorphToMany = Mockery::mock(MorphToMany::class);

        // Act
        $category
            ->shouldReceive('delete')
            ->once();
        $category
            ->shouldReceive('images')
            ->once()
            ->andReturn($imagesMorphToMany);
        $imagesMorphToMany
            ->shouldReceive('detach')
            ->once();

        $result = $this->categoryRepository->destroy($category);

        // Assert
        $this->assertNull($result);
    }

    public function testGetModel()
    {
        // Arrange
        $repository = new CategoryRepository($this->builderFactory);
        $method = $this->getInaccessibleMethod($repository, 'getModel');

        // Act
        $result = $method->invoke($repository);

        // Assert
        $this->assertEquals(ConcreteCategory::class, $result);
    }

    public function testCreate()
    {
        // Arrange
        $repository = $this->getMockBuilder(CategoryRepository::class)
            ->onlyMethods(['getModel'])
            ->setConstructorArgs([$this->builderFactory])
            ->getMock();

        $method = $this->getInaccessibleMethod($repository, 'create');

        // Act
        $repository
            ->expects($this->once())
            ->method('getModel')
            ->willReturn(ConcreteCategory::class);

        $result = $method->invoke($repository);

        // Assert
        $this->assertInstanceOf(Category::class, $result);
    }

    /*
     * Protected Methods
     */
    protected function testIndex(array $filters)
    {
        // Arrange
        $category = $this->mockCategory();
        $queryBuilder = $this->mock(\Illuminate\Database\Eloquent\Builder::class);
        $expectedCollection = new Collection([$category]);
        $expectedLengthAwarePaginator = new ConcreteLengthAwarePaginator(
            $expectedCollection,
            $expectedCollection->count(),
            $filters['rowsPerPage'] ?? config('category.default-rows-per-page'),
            $filters['page']
        );

        // Act
        $this->categoryRepository
            ->expects($this->once())
            ->method('getModel')
            ->willReturn(get_class($category));

        $category
            ->shouldReceive('filter')
            ->once()
            ->with($filters)
            ->andReturn($queryBuilder);

        $queryBuilder
            ->shouldReceive('paginate')
            ->once()
            ->with($filters['rowsPerPage'] ?? config('category.default-rows-per-page'))
            ->andReturn($expectedLengthAwarePaginator);

        $result = $this->categoryRepository->index($filters);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(1, $result);
        $this->assertTrue($result->contains($category));
        $this->assertEquals($category, $result->first());
    }

    protected function testStore(?CategoryType $categoryType)
    {
        // Arrange
        $fill = $this->categoryFillData();
        $category = $this->mockCategory();
        $builder = $this->mockCategoryBuilder();
        $metadata = Mockery::mock(Metadata::class);

        // Act
        $this->categoryRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn($category);

        $this->builderFactory
            ->shouldReceive('create')
            ->once()
            ->with($fill, $category)
            ->andReturn($builder);

        $builder
            ->shouldReceive('forStore')
            ->once()
            ->with($categoryType, $metadata);

        $category
            ->shouldReceive('save')
            ->once();

        $result = $this->categoryRepository->store($fill, $categoryType, $metadata);

        // Assert
        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals($category, $result);
    }

    protected function mockCategory()
    {
        return $this->mock(Category::class);
    }

    protected function mockCategoryBuilder()
    {
        return $this->mock(\Hexatex\LaravelCategory\Category\Builders\Builder::class);
    }

    protected function mockCategoryType()
    {
        return $this->mock(CategoryType::class);
    }

    protected function categoryFillData()
    {
        return CategoryFactory::new()->make()->toArray();
    }
}
