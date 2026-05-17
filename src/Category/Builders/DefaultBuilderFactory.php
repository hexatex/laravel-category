<?php

namespace Hexatex\LaravelCategory\Category\Builders;

use Hexatex\LaravelCategory\Category\Contracts\Category;

class DefaultBuilderFactory implements BuilderFactory
{
    public function create(array $fill, Category $category): Builder
    {
        return new DefaultBuilder($fill, $category);
    }
}
