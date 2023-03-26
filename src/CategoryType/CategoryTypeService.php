<?php

namespace Hexatex\LaravelCategory\CategoryType;

use Hexatex\LaravelCategory\CategoryType\CategoryType;

interface CategoryTypeService
{
    public function store(array $fill): CategoryType;
    public function update(array $fill, CategoryType $dynamic): void;
    public function destroy(CategoryType $dynamic): void;
}
