<?php

namespace Hexatex\LaravelCategory\Category\Builders;

use Hexatex\LaravelCategory\Category\Contracts\Category;

interface BuilderFactory
{
    public function create(array $fill, Category $category): Builder;
}
