<?php

namespace Hexatex\LaravelCategory;

use Hexatex\LaravelCategory\Commands\LaravelCategoryCommand;
use Hexatex\LaravelCategory\CategoryType\Regular;
use Hexatex\LaravelCategory\CategoryType\RegularService;
use Hexatex\LaravelCategory\CategoryType\Dynamic;
use Hexatex\LaravelCategory\CategoryType\DynamicService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelCategoryServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-category')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-category_table')
            ->hasCommand(LaravelCategoryCommand::class);
    }

    public function boot(): void
    {
        App::bind('LaravelCategory', function()
        {
            return new \Hexatex\LaravelCategory\CategoryService;
        });

        LaravelCategory::setCategoryTypes([ // Configure using morph map key and CategoryServiceClass class
            'category_regular' => RegularService::class,
            'category_dynamic' => DynamicService::class,
        ]);
    }
}
