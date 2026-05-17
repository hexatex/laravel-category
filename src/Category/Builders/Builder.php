<?php

namespace Hexatex\LaravelCategory\Category\Builders;

use Hexatex\LaravelCategory\CategoryType\CategoryType;
use Hexatex\LaravelCategory\Category\Contracts\Category;
use Hexatex\LaravelMetadata\Metadata\Contracts\Metadata;

interface Builder
{
    public function __construct(array $fill, Category $model);
    public function forStore(?CategoryType $categoryType, Metadata $metadata): void;
    public function forUpdate(): void;
}
