<?php

namespace Hexatex\LaravelCategory\Category\Requests\Admin;

use Hexatex\LaravelMisc\IndexRequest as BaseIndexRequest;

class IndexRequest extends BaseIndexRequest
{
    protected $maxRowsConfig = 'category.max-rows-per-page';
}
