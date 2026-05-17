<?php

namespace Hexatex\LaravelCategory\Tests\Unit\CategoryType;

use Hexatex\LaravelCategory\CategoryType\CategoryTypeService;
use Hexatex\LaravelCategory\CategoryType\DefaultTypeServiceFactory;
use Hexatex\LaravelCategory\Tests\TestCase;

class DefaultTypeServiceFactoryTest extends TestCase
{
    public function testCreate_nullTypeService()
    {
        // Arrange
        $serviceFactory = new DefaultTypeServiceFactory;

        // Act
        $result = $serviceFactory->create(null);

        $categoryType = $result->store([]);
        $result->update([], $categoryType);
        $result->destroy($categoryType);

        // Assert
        $this->assertInstanceOf(CategoryTypeService::class, $result);
        $this->assertEquals('Hexatex\LaravelCategory\CategoryType\NullTypeService', get_class($result));
    }
}
