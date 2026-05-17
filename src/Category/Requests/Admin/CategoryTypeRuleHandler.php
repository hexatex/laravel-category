<?php

namespace Hexatex\LaravelCategory\Category\Requests\Admin;

use Hexatex\LaravelCategory\AdminCategoryTypeRequests;
use Hexatex\LaravelMisc\FormRequest;
use Hexatex\LaravelValidation\RuleHandlers\TypeRuleHandler;

class CategoryTypeRuleHandler extends TypeRuleHandler
{
    /*
     * Protected Methods
     */
    protected function getTypeRequest(?string $morphClass): FormRequest
    {
        return AdminCategoryTypeRequests::get($morphClass);
    }
}
