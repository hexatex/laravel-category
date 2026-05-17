<?php

namespace Hexatex\LaravelCategory\CategoryType;

interface TypeServiceFactory
{
    public function create(?string $type): CategoryTypeService;
}
