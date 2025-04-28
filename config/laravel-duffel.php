<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'access_token' => env('DUFFEL_ACCESS_TOKEN', ''),
    'api_url' => env('DUFFEL_API_URL', 'https://api.duffel.com/'),
    'url_prefix' => env('DUFFEL_URL_PREFIX', 'air'),
    'version' => env('DUFFEL_API_VERSION', 'v1'),
];