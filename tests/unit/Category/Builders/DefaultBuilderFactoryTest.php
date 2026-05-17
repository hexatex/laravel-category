<?php

namespace Hexatex\LaravelCategory\Tests\Unit\Category\Builders;

use Hexatex\LaravelCategory\Category\Builders\DefaultBuilder;
use Hexatex\LaravelCategory\Category\Builders\DefaultBuilderFactory;
use Hexatex\LaravelCategory\Factories\CategoryFactory;
use Hexatex\LaravelCategory\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DefaultBuilderFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function testCreate()
    {
        // Arrange
        $fill = CategoryFactory::new()->make()->toArray();
        $category = CategoryFactory::new()->make();

        $builderFactory = new DefaultBuilderFactory();

        // Act
        $resizer = $builderFactory->create($fill, $category);

        // Assert
        $this->assertInstanceOf(DefaultBuilder::class, $resizer);
    }
}
