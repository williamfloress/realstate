<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Set to '*' to trust all proxies (required for Railway, Heroku, etc.)
    | or specify IP addresses to trust.
    |
    */

    'proxies' => env('TRUSTED_PROXIES', null),

];
