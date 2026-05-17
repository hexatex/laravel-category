<?php

namespace Hexatex\LaravelCategory\Category\Requests\Admin;

use Hexatex\LaravelMisc\FormRequest;

/**
 * @see UpdateRequest
 */
class StoreRequest extends UpdateRequest
{
    /*
     * Protected Methods
     */
    protected function typeMorphClass(): ?string
    {
        return $this->input('type.morph_class');
    }
}
