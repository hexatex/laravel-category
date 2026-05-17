<?php

namespace Hexatex\LaravelCategory\CategoryType;

use Hexatex\LaravelCategory\CategoryTypeServices;

class DefaultTypeServiceFactory implements TypeServiceFactory
{
    public function create(?string $type): CategoryTypeService
    {
        $serviceClass = CategoryTypeServices::get($type) ?: NullTypeService::class;

        return resolve($serviceClass);
    }
}

/**
 * A null implementation of CategoryTypeService.
 */
class NullTypeService implements CategoryTypeService
{
    public function store(array $fill): ?CategoryType
    {
        return null; // Do nothing
    }

    public function update(array $fill, ?CategoryType $categoryType): void
    {
        // Do nothing
    }

    public function destroy(?CategoryType $categoryType): void
    {
        // Do nothing
    }
}
