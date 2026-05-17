<?php

namespace Hexatex\LaravelCategory\CategoryItem\Requests\Admin;

use Hexatex\LaravelMisc\FormRequest;

class CategoryItemRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => ['sometimes', 'nullable', 'exists:category_items,id'],
            'category.id' => ['required', 'exists:categories,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'category.id' => 'category',
        ];
    }
}
