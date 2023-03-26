<?php

namespace Hexatex\LaravelCategory;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Hexatex\LaravelCategory\Commands\LaravelCategoryCommand;

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

        LaravelCategory::setCategoryTypes([
            // Todo: should I be defining model and service here, or have service class defined on the CategoryType interface
            'regular' => \Hexatex\LaravelCategory\CategoryType\Regular::class,
            'dynamic' => \Hexatex\LaravelCategory\CategoryType\Dynamic::class,
        ]);
    }
}
