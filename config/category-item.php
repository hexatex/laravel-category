<?php

return [
    'default-rows-per-page' => env('CATEGORY_ITEM_DEFAULT_ROWS_PER_PAGE', env('CATEGORY_DEFAULT_ROWS_PER_PAGE', 20)),
    'max-rows-per-page' => env('CATEGORY_ITEM_MAX_ROWS_PER_PAGE', env('CATEGORY_MAX_ROWS_PER_PAGE', 500)),
];
