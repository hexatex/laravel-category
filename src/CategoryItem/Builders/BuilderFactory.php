<?php

namespace Hexatex\LaravelCategory\CategoryItem\Builders;

use Hexatex\LaravelCategory\CategoryItem\Contracts\CategoryItem;

interface BuilderFactory
{
    public function create(array $fill, CategoryItem $categoryItem): Builder;
}
