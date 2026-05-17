<?php

namespace Hexatex\LaravelCategory\Category\Builders;

use Hexatex\LaravelCategory\CategoryType\CategoryType;
use Hexatex\LaravelCategory\Category\Contracts\Category;
use Hexatex\LaravelMetadata\Metadata\Contracts\Metadata;
use Hexatex\LaravelMisc\Patterns\Builder as BaseBuilder;

class DefaultBuilder extends BaseBuilder implements Builder
{
    public function __construct(array $fill, Category $model)
    {
        $this->fill = $fill;
        $this->model = $model;
    }

    public function forStore(?CategoryType $categoryType, Metadata $metadata): void
    {
        $this->forUpdate();
        $this->associate('type', $categoryType, nullable: true);
        $this->associate('metadata', $metadata);
    }

    public function forUpdate(): void
    {
        $this->fill();
        $this->associateId('mainImage', nullable: true);
        $this->associateId('parent', nullable: true);
    }
}
