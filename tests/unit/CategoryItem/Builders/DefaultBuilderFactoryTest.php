<?php

namespace Hexatex\LaravelCategory\Tests\Unit\CategoryItem\Builders;

use Hexatex\LaravelCategory\CategoryItem\Builders\DefaultBuilder;
use Hexatex\LaravelCategory\CategoryItem\Builders\DefaultBuilderFactory;
use Hexatex\LaravelCategory\Factories\CategoryItemFactory;
use Hexatex\LaravelCategory\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DefaultBuilderFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function testCreate()
    {
        // Arrange
        $fill = CategoryItemFactory::new()->make()->toArray();
        $categoryItem = CategoryItemFactory::new()->make();

        $builderFactory = new DefaultBuilderFactory();

        // Act
        $resizer = $builderFactory->create($fill, $categoryItem);

        // Assert
        $this->assertInstanceOf(DefaultBuilder::class, $resizer);
    }
}
