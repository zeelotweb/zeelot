<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Package Discount
    |--------------------------------------------------------------------------
    |
    | Applied automatically to every package selection before any discount
    | code is entered. Expressed as a whole-number percentage.
    |
    */

    'default_percentage' => (float) env('DEFAULT_DISCOUNT_PERCENTAGE', 10),

    /*
    |--------------------------------------------------------------------------
    | Discount Category Types
    |--------------------------------------------------------------------------
    |
    | The kinds of value a discount category can carry. Super admins pick one
    | of these when creating a category from the admin Discounts page.
    |
    */

    'types' => ['percentage', 'fixed'],

    /*
    |--------------------------------------------------------------------------
    | Bounds
    |--------------------------------------------------------------------------
    |
    | Guardrails applied when a category or code is created, regardless of
    | who's creating it.
    |
    */

    'max_percentage' => 100,

];
