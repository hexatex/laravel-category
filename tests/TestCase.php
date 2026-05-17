<?php

namespace Hexatex\LaravelCategory\Tests;

use Hexatex\LaravelCategory\AdminCategorizableResources;
use Hexatex\LaravelCategory\AdminCategoryTypeRequests;
use Hexatex\LaravelCategory\AdminCategoryTypeResources;
use Hexatex\LaravelCategory\CategoryTypeServices;
use Hexatex\LaravelCategory\LaravelCategoryServiceProvider;
use Hexatex\LaravelCategory\Tests\TestObjects\TestCategorizable;
use Hexatex\LaravelCategory\Tests\TestObjects\TestCategorizableResource;
use Hexatex\LaravelCategory\Tests\TestObjects\TestCategoryType;
use Hexatex\LaravelCategory\Tests\TestObjects\TestCategoryTypeRequest;
use Hexatex\LaravelCategory\Tests\TestObjects\TestCategoryTypeResource;
use Hexatex\LaravelCategory\Tests\TestObjects\TestCategoryTypeService;
use Hexatex\LaravelCategory\WebCategorizableResources;
use Hexatex\LaravelCategory\WebCategoryTypeRequests;
use Hexatex\LaravelCategory\WebCategoryTypeResources;
use Hexatex\LaravelImage\LaravelImageServiceProvider;
use Hexatex\LaravelMetadata\LaravelMetadataServiceProvider;
use Hexatex\LaravelSlug\LaravelSlugServiceProvider;
use Hexatex\LaravelUser\LaravelUserServiceProvider;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Orchestra\Testbench\TestCase as Orchestra;
use \ReflectionClass;

abstract class TestCase extends Orchestra
{
    use DatabaseMigrations;

    protected function getPackageProviders($app)
    {
        return [
            LaravelCategoryServiceProvider::class,
            LaravelImageServiceProvider::class,
            LaravelMetadataServiceProvider::class,
            LaravelSlugServiceProvider::class,
            LaravelUserServiceProvider::class,
            \Laravel\Passport\PassportServiceProvider::class,
            \Spatie\Permission\PermissionServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:jqAF7i3K4aZ+6h/jN/jl9ZQRCn1c3LPGACxe+x7FpBQ=');
    }

    protected function getInaccessibleMethod($class, string $methodName)
    {
        $reflection = new ReflectionClass($class);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Categorizable
        WebCategorizableResources::register(TestCategorizable::class, TestCategorizableResource::class);
        AdminCategorizableResources::register(TestCategorizable::class, TestCategorizableResource::class);

        // CategoryType
        WebCategoryTypeResources::register(TestCategoryType::class, TestCategoryTypeResource::class);
        AdminCategoryTypeResources::register(TestCategoryType::class, TestCategoryTypeResource::class);
        WebCategoryTypeRequests::register('test-category-type', TestCategoryTypeRequest::class);
        AdminCategoryTypeRequests::register('test-category-type', TestCategoryTypeRequest::class);
        CategoryTypeServices::register(TestCategoryType::class, TestCategoryTypeService::class);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
