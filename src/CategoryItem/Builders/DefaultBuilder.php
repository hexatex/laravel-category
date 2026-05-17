<?php

namespace Hexatex\LaravelCategory\CategoryItem\Builders;

use Hexatex\LaravelCategory\CategoryItem\Contracts\Categorizable;
use Hexatex\LaravelCategory\CategoryItem\Contracts\CategoryItem;
use Hexatex\LaravelMisc\Patterns\Builder as BaseBuilder;
use \InvalidArgumentException;

class DefaultBuilder extends BaseBuilder implements Builder
{
    public function __construct(array $fill, CategoryItem $model)
    {
        $this->fill = $fill;
        $this->model = $model;
    }

    /**
     * The 'category' id is required in the fill array.
     * 
     * @throws InvalidArgumentException
     */
    public function forStore(Categorizable $categorizable): void
    {
        $this->associateId('category');
        $this->associate('categorizable', $categorizable);
    }

    public function forUpdate(): void
    {
    }
}
