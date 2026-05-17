<?php

namespace Hexatex\LaravelCategory\CategoryType;

interface CategoryTypeService
{
    public function store(array $fill): ?CategoryType;
    public function update(array $fill, ?CategoryType $categoryType): void;
    public function destroy(?CategoryType $categoryType): void;
}
