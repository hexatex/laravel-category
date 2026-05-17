<?php

namespace Hexatex\LaravelCategory\Tests\TestObjects;

use Hexatex\LaravelMisc\FormRequest;

class TestCategoryTypeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'test' => ['required', 'string', 'max:191'],
            'morph_class' => ['required', 'in:test-category-type'],
        ];
    }

    public function attributes(): array
    {
        return ['test' => 'test-attribute'];
    }
}
