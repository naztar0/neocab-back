<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'app' => [
        'url' => env('APP_URL'),
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'khpi' => [
        'api_main' => env('KHPI_API_MAIN'),
        'api_sport' => env('KHPI_API_SPORT'),
        'api_schedule' => env('KHPI_API_SCHEDULE'),
        'api_docs' => env('KHPI_API_DOCS'),
        'api_params' => env('KHPI_API_PARAMS'),
    ],

    'azure' => [
        'client_id' => env('AZURE_CLIENT_ID'),
        'client_secret' => env('AZURE_CLIENT_SECRET'),
        'redirect_uri' => env('AZURE_REDIRECT_URI'),
        'authority' => env('AZURE_AUTHORITY'),
        'authorize_endpoint' => env('AZURE_AUTHORIZE_ENDPOINT'),
        'token_endpoint' => env('AZURE_TOKEN_ENDPOINT'),
        'scopes' => env('AZURE_SCOPES'),
        'scopes_app' => env('AZURE_SCOPES_APP'),
    ],

];
