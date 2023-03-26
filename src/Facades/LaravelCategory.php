<?php

namespace Hexatex\LaravelCategory\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Hexatex\LaravelCategory\LaravelCategory
 */
class LaravelCategory extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Hexatex\LaravelCategory\CategoryService::class;
    }
}
