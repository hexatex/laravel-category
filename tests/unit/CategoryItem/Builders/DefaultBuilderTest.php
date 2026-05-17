<?php

namespace Hexatex\LaravelCategory\Tests\Unit\CategoryItem\Builders;

use Hexatex\LaravelCategory\CategoryItem\Builders\DefaultBuilder;
use Hexatex\LaravelCategory\CategoryItem\CategoryItem;
use Hexatex\LaravelCategory\CategoryItem\Contracts\Categorizable;
use Hexatex\LaravelCategory\Factories\CategoryItemFactory;
use Hexatex\LaravelCategory\Tests\TestCase;
use Hexatex\LaravelMisc\Contracts\Authenticatable;
use Hexatex\LaravelMisc\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use \InvalidArgumentException;
use \Mockery;

class DefaultBuilderTest extends TestCase
{
    public function testForStore()
    {
        // Arrange
        $fill = [
            'category' => ['id' => 12345],
        ];
        $categoryItem = Mockery::mock(CategoryItem::class);
        $builder = new DefaultBuilder($fill, $categoryItem);
        $categorizable = Mockery::mock(Categorizable::class);
        $categorizableMorphTo = Mockery::mock(MorphTo::class);

        $categoryBelongsTo = Mockery::mock(BelongsTo::class);

        // Act
        $categoryItem
            ->shouldReceive('category')
            ->once()
            ->andReturn($categoryBelongsTo);

        $categoryBelongsTo
            ->shouldReceive('associate')
            ->with($fill['category']['id'] ?? null)
            ->once();

        $categoryItem
            ->shouldReceive('categorizable')
            ->once()
            ->andReturn($categorizableMorphTo);

        $categorizableMorphTo
            ->shouldReceive('associate')
            ->once()
            ->with($categorizable);

        $builder->forStore($categorizable);

        $this->addToAssertionCount(1);
    }

    public function testForUpdate()
    {
        $fill = [];
        $categoryItem = Mockery::mock(CategoryItem::class);
        $builder = new DefaultBuilder($fill, $categoryItem);

        $builder->forUpdate();

        $this->addToAssertionCount(1);
    }
}
