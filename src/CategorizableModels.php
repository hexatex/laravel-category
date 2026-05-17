<?php

namespace Hexatex\LaravelCategory;

use Hexatex\LaravelMisc\Managers\ModelManager;

/**
 * @see ModelManager
 */
class CategorizableModels extends ModelManager
{
    protected static array $models = []; // Todo we might not need this class, check to see if it is used then delete it if not
}
