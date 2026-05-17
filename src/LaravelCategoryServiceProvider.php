<?php

namespace Hexatex\LaravelCategory;

use Hexatex\LaravelCategory\CategoryItem\Builders\Builder as CategoryItemBuilder;
use Hexatex\LaravelCategory\CategoryItem\Builders\BuilderFactory as CategoryItemBuilderFactory;
use Hexatex\LaravelCategory\CategoryItem\Builders\DefaultBuilder as CategoryItemDefaultBuilder;
use Hexatex\LaravelCategory\CategoryItem\Builders\DefaultBuilderFactory as CategoryItemDefaultBuilderFactory;
use Hexatex\LaravelCategory\CategoryItem\CategoryItem as ConcreteCategoryItem;
use Hexatex\LaravelCategory\CategoryItem\CategoryItemRepository as ConcreteCategoryItemRepository;
use Hexatex\LaravelCategory\CategoryItem\CategoryItemService as ConcreteCategoryItemService;
use Hexatex\LaravelCategory\CategoryItem\Contracts\CategoryItem;
use Hexatex\LaravelCategory\CategoryItem\Contracts\CategoryItemRepository;
use Hexatex\LaravelCategory\CategoryItem\Contracts\CategoryItemService;
use Hexatex\LaravelCategory\CategoryType\DefaultTypeServiceFactory;
use Hexatex\LaravelCategory\CategoryType\TypeServiceFactory;
use Hexatex\LaravelCategory\Category\Builders\Builder;
use Hexatex\LaravelCategory\Category\Builders\BuilderFactory;
use Hexatex\LaravelCategory\Category\Builders\DefaultBuilder;
use Hexatex\LaravelCategory\Category\Builders\DefaultBuilderFactory;
use Hexatex\LaravelCategory\Category\Category as ConcreteCategory;
use Hexatex\LaravelCategory\Category\CategoryRepository as ConcreteCategoryRepository;
use Hexatex\LaravelCategory\Category\CategoryService as ConcreteCategoryService;
use Hexatex\LaravelCategory\Category\Contracts\Category;
use Hexatex\LaravelCategory\Category\Contracts\CategoryRepository;
use Hexatex\LaravelCategory\Category\Contracts\CategoryService;
use Hexatex\LaravelCategory\Tests\TestObjects\TestCategorizable;
use Hexatex\LaravelCategory\Tests\TestObjects\TestCategoryType;
use Hexatex\LaravelMenu\MenuItemableModels;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LaravelCategoryServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'hexatex');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'hexatex');

        Route::macro('bindCategoryModel', function () {
            Route::model('category', ConcreteCategory::class);
        });

        Relation::enforceMorphMap([
            'laravel-category' => ConcreteCategory::class,
            'laravel-category-item' => ConcreteCategoryItem::class,
        ]);

        $this->loadRoutesFrom(__DIR__.'/../routes/category.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
            $this->bootForConsole();

            if ($this->app->environment('testing')) {
                Relation::enforceMorphMap([
                    'test-categorizable' => TestCategorizable::class,
                    'test-category-type' => TestCategoryType::class,
                ]);

                $this->loadMigrationsFrom(__DIR__.'/../database/migrations/testing');
            }
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/category.php', 'category');
        $this->mergeConfigFrom(__DIR__.'/../config/category-item.php', 'category-item');

        // Category
        $this->app->bind(Builder::class, DefaultBuilder::class);
        $this->app->bind(BuilderFactory::class, DefaultBuilderFactory::class);
        $this->app->bind(Category::class, ConcreteCategory::class);
        $this->app->bind(CategoryRepository::class, ConcreteCategoryRepository::class);
        $this->app->bind(CategoryService::class, ConcreteCategoryService::class);

        // CategoryItem
        $this->app->bind(CategoryItemBuilder::class, CategoryItemDefaultBuilder::class);
        $this->app->bind(CategoryItemBuilderFactory::class, CategoryItemDefaultBuilderFactory::class);
        $this->app->bind(CategoryItem::class, ConcreteCategoryItem::class);
        $this->app->bind(CategoryItemRepository::class, ConcreteCategoryItemRepository::class);
        $this->app->bind(CategoryItemService::class, ConcreteCategoryItemService::class);

        // CategoryType
        $this->app->bind(TypeServiceFactory::class, DefaultTypeServiceFactory::class);

        MenuItemableModels::register(ConcreteCategory::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/category.php' => config_path('category.php'),
            __DIR__.'/../config/category-item.php' => config_path('category-item.php'),
        ], 'laravel-category.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/hexatex'),
        ], 'category.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/hexatex'),
        ], 'category.assets');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/hexatex'),
        ], 'category.lang');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
