<?php

namespace Hexatex\LaravelCategory\CategoryItem\Builders;

use Hexatex\LaravelCategory\CategoryItem\Contracts\CategoryItem;

class DefaultBuilderFactory implements BuilderFactory
{
    public function create(array $fill, CategoryItem $categoryItem): Builder
    {
        return new DefaultBuilder($fill, $categoryItem);
    }
}
