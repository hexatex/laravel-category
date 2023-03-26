<?php

namespace Hexatex\LaravelCategory\Commands;

use Illuminate\Console\Command;

class LaravelCategoryCommand extends Command
{
    public $signature = 'laravel-category';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
