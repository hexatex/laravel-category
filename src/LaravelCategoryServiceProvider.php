<?php

namespace Hexatex\LaravelCategory;

use Hexatex\LaravelCategory\Commands\LaravelCategoryCommand;
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
}
