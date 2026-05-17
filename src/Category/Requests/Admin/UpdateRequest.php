<?php

namespace Hexatex\LaravelCategory\Category\Requests\Admin;

use Hexatex\LaravelCategory\Category\Requests\Admin\CategoryTypeRuleHandler;
use Hexatex\LaravelMetadata\Metadata\Requests\Admin\MetadataRequest;
use Hexatex\LaravelMisc\FormRequest;
use Hexatex\LaravelSlug\Slug\Requests\Admin\SlugRequest;

class UpdateRequest extends FormRequest
{
    public function __construct(protected CategoryTypeRuleHandler $typeRuleHandler)
    {}

    public function rules()
    {
        $rules = [
            'title' => ['required', 'string', 'max:191'],
            'page_title' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:5000'],
            'sort_by' => ['required', 'string', 'max:191'],
            'sort_desc' => ['required', 'bool'],
            'is_hidden' => ['required', 'bool'],

            // Relationships
            'mainImage.id' => ['nullable', 'exists:images,id'],
            'parent.id' => ['nullable', 'exists:categories,id'],
        ];

        foreach ((new SlugRequest)->rules() as $key => $rule) {
            $rules['slug.' . $key] = $rule;
        }

        foreach ((new MetadataRequest)->rules() as $key => $rule) {
            $rules['metadata.' . $key] = $rule;
        }

        return $this->typeRuleHandler->merge($rules, $this->typeMorphClass());
    }

    public function attributes(): array
    {
        return $this->typeRuleHandler->mergeAttributes([
            'mainImage.id' => 'image',
            'parent.id' => 'image',
        ], $this->typeMorphClass());
    }

    /*
     * Protected Methods
     */
    protected function typeMorphClass(): ?string
    {
        return $this->route('category')?->type?->getMorphClass();
    }
}
