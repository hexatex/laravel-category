<?php

namespace Hexatex\LaravelCategory\Tests\Unit\Category;

use Hexatex\LaravelCategory\CategoryType\CategoryType;
use Hexatex\LaravelCategory\CategoryType\CategoryTypeService;
use Hexatex\LaravelCategory\CategoryType\TypeServiceFactory;
use Hexatex\LaravelCategory\Category\Category;
use Hexatex\LaravelCategory\Category\CategoryRepository;
use Hexatex\LaravelCategory\Category\CategoryService;
use Hexatex\LaravelCategory\Factories\CategoryFactory;
use Hexatex\LaravelCategory\Tests\TestCase;
use Hexatex\LaravelMetadata\Factories\MetadataFactory;
use Hexatex\LaravelMetadata\Metadata\Contracts\Metadata;
use Hexatex\LaravelMetadata\Metadata\Contracts\MetadataService;
use Hexatex\LaravelSlug\Factories\SlugFactory;
use Hexatex\LaravelSlug\Slug\Contracts\SlugService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use \Mockery;

class CategoryServiceTest extends TestCase
{
    protected $categoryRepository;
    protected $typeServiceFactory;
    protected $categoryService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = $this->mock(CategoryRepository::class);
        $this->typeServiceFactory = $this->mock(TypeServiceFactory::class);
        $this->slugService = $this->mock(SlugService::class);
        $this->metadataService = $this->mock(MetadataService::class);

        $this->categoryService = new CategoryService(
            $this->categoryRepository,
            $this->typeServiceFactory,
            $this->slugService,
            $this->metadataService,
        );
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

        $categoryMock = $this->mockCategory();
        $collection = new Collection([$categoryMock]);

        // Act
        $this->categoryRepository
            ->shouldReceive('index')
            ->once()
            ->with($filters)
            ->andReturn($collection);

        $result = $this->categoryService->index($filters);

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertTrue($result->contains($categoryMock));
        $this->assertEquals($categoryMock, $result->first());
    }

    public function testStore()
    {
        // Arrange
        $fill = [
            'title' => 'Test Category',
            'type' => ['morph_class' => 'test-unknown-type'],
            'metadata' => ['test' => 'test-value'],
            'slug' => ['test' => 'test-value'],
        ];
        $categoryType = $this->mock(CategoryType::class);
        $categoryTypeService = $this->mock(CategoryTypeService::class);
        $category = $this->mockCategory();
        $metadata = Mockery::mock(Metadata::class);

        // Act
        $this->metadataService
            ->shouldReceive('store')
            ->once()
            ->with($fill['metadata'])
            ->andReturn($metadata);

        $this->typeServiceFactory
            ->shouldReceive('create')
            ->once()
            ->with('test-unknown-type')
            ->andReturn($categoryTypeService);

        $categoryTypeService
            ->shouldReceive('store')
            ->once()
            ->with($fill['type'])
            ->andReturn($categoryType);

        $this->categoryRepository
            ->shouldReceive('store')
            ->once()
            ->with($fill, $categoryType, $metadata)
            ->andReturn($category);

        $this->slugService
            ->shouldReceive('store')
            ->once()
            ->with($fill['slug'], $category);

        $result = $this->categoryService->store($fill);

        // Assert
        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals($category, $result);
    }

    public function testUpdate()
    {
        // Arrange
        $fill = [
            'title' => 'Updated Category',
            'type' => ['test' => 'test-string'],
            'metadata' => ['test' => 'test-value'],
            'slug' => ['test' => 'test-value'],
        ];
        $categoryType = $this->mock(CategoryType::class);
        $category = $this->mockCategory($categoryType);
        $categoryTypeService = $this->mock(CategoryTypeService::class);

        // Act
        $this->metadataService
            ->shouldReceive('update')
            ->once()
            ->with($fill['metadata'], $category->metadata);

        $this->typeServiceFactory
            ->shouldReceive('create')
            ->once()
            ->with('default')
            ->andReturn($categoryTypeService);

        $categoryTypeService
            ->shouldReceive('update')
            ->once()
            ->with($fill['type'], $categoryType)
            ->andReturnNull();

        $this->categoryRepository
            ->shouldReceive('update')
            ->once()
            ->with($fill, $category);

        $this->slugService
            ->shouldReceive('update')
            ->once()
            ->with($fill['slug'], $category->slug);

        $result = $this->categoryService->update($fill, $category);

        // Assert
        $this->assertNull($result);
    }

    public function testDestroy()
    {
        // Arrange
        $category = $this->mockCategory();

        // Act
        $this->categoryRepository
            ->shouldReceive('destroy')
            ->once()
            ->with($category);

        $this->metadataService
            ->shouldReceive('destroy')
            ->once()
            ->with($category->metadata);

        $this->slugService
            ->shouldReceive('destroy')
            ->once()
            ->with($category->slug);

        $result = $this->categoryService->destroy($category);

        // Assert
        $this->assertNull($result);
    }

    protected function mockCategory(CategoryType $categoryType = null)
    {
        $category = CategoryFactory::new()->make();

        if ($categoryType) {
            $category->type()->associate($categoryType);
            $category->type_type = 'default';
        }

        $metadata = MetadataFactory::new()->make();
        $category->setRelation('metadata', $metadata);

        $slug = SlugFactory::new()->make();
        $category->setRelation('slug', $slug);

        return Mockery::mock($category);
    }
}
