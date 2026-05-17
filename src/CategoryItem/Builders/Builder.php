<?php

namespace Hexatex\LaravelCategory\CategoryItem\Builders;

use Hexatex\LaravelCategory\CategoryItem\Contracts\Categorizable;
use Hexatex\LaravelCategory\CategoryItem\Contracts\CategoryItem;

interface Builder
{
    public function __construct(array $fill, CategoryItem $model);
    public function forStore(Categorizable $categorizable): void;
    public function forUpdate(): void;
}
