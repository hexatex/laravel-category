<?php

namespace Hexatex\LaravelCategory\Tests\Unit\Category\Builders;

use Hexatex\LaravelCategory\CategoryType\CategoryType;
use Hexatex\LaravelCategory\Category\Builders\DefaultBuilder;
use Hexatex\LaravelCategory\Category\Contracts\Category;
use Hexatex\LaravelCategory\Factories\CategoryFactory;
use Hexatex\LaravelCategory\Tests\TestCase;
use Hexatex\LaravelMetadata\Metadata\Contracts\Metadata;
use Hexatex\LaravelMisc\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \Mockery;

class DefaultBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected $category;

    public function setUp(): void
    {
        parent::setUp();

        $this->category = CategoryFactory::new()->make();
        $this->category = Mockery::mock($this->category);
    }

    public function testForStore_hasCategoryType()
    {
        $categoryType = Mockery::mock(CategoryType::class);
        $fill = $this->getFill();
        
        $this->testForStore($fill, $categoryType);
    }

    public function testForStore_noCategoryType()
    {
        $fill = $this->getFill();
        
        $this->testForStore($fill, null);
    }

    public function testForStore_hasRelations()
    {
        $fill = $this->getFill();
        $fill['mainImage']['id'] = 'main-image-id';
        $fill['parent']['id'] = 'parent-id';

        $this->testForStore($fill, null);
    }

    public function testForUpdate_noRelations()
    {
        $fill = $this->getFill();
        
        $this->testForUpdate($fill);
    }

    public function testForUpdate_hasRelations()
    {
        $fill = $this->getFill();
        $fill['mainImage']['id'] = 'main-image-id';
        $fill['parent']['id'] = 'parent-id';

        $this->testForUpdate($fill);
    }

    /*
     * Protected Methods
     */
    protected function testForStore(array $fill, ?CategoryType $categoryType)
    {
        // Arrange
        $builder = $this->createDefaultBuilder($fill);
        $categoryMorphTo = Mockery::mock(MorphTo::class);
        $metadata = Mockery::mock(Metadata::class);
        $metadataBelongsTo = Mockery::mock(BelongsTo::class);

        // Arrange and Act
        $this->forStoreForBuildCommon($fill);

        // Act
        $this->category
            ->shouldReceive('type')
            ->once()
            ->andReturn($categoryMorphTo);

        $categoryMorphTo
            ->shouldReceive('associate')
            ->with($categoryType)
            ->once()
            ->andReturn($this->category);

        $this->category
            ->shouldReceive('metadata')
            ->once()
            ->andReturn($metadataBelongsTo);

        $metadataBelongsTo
            ->shouldReceive('associate')
            ->once()
            ->with($metadata);

        $result = $builder->forStore($categoryType, $metadata);

        // Assert
        $this->assertNull($result);
    }

    protected function testForUpdate(array $fill)
    {
        // Arrange
        $builder = $this->createDefaultBuilder($fill);

        // Arrange and Act
        $this->forStoreForBuildCommon($fill);

        // Act
        $result = $builder->forUpdate();

        // Assert
        $this->assertNull($result);
    }

    protected function forStoreForBuildCommon(array $fill)
    {
        // Arrange
        $mainImageBelongsTo = $this->mockBelongsTo();
        $parentBelongsTo = $this->mockBelongsTo();

        // Act
        $this->category
            ->shouldReceive('fill')
            ->with($fill)
            ->andReturnNull();

        $this->category
            ->shouldReceive('mainImage')
            ->once()
            ->andReturn($mainImageBelongsTo);

        $mainImageBelongsTo
            ->shouldReceive('associate')
            ->with($fill['mainImage']['id'] ?? null)
            ->andReturn($this->category);

        $this->category
            ->shouldReceive('parent')
            ->once()
            ->andReturn($parentBelongsTo);

        $parentBelongsTo
            ->shouldReceive('associate')
            ->with($fill['parent']['id'] ?? null)
            ->andReturn($this->category);
    }

    protected function createDefaultBuilder(array $fill)
    {
        return new DefaultBuilder($fill, $this->category);
    }

    protected function mockBelongsTo()
    {
        return Mockery::mock(BelongsTo::class);
    }

    protected function getFill()
    {
        return CategoryFactory::new()->make()->toArray();
    }
}
