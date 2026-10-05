<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    |
    | The in-memory lookup API (the Airlines facade) works without a database.
    | These settings only apply if you publish the migration and seed the
    | airlines into a table, e.g. so other tables can reference them.
    |
    */

    'table' => env('AIRLINES_TABLE', 'airlines'),

    'connection' => env('AIRLINES_DB_CONNECTION'),

];
