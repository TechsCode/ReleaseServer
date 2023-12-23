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

    'techscode' => [
        'maven' => [
            'url' => env('TECHSCODE_MAVEN_URL'),
            'username' => env('TECHSCODE_MAVEN_USERNAME'),
            'password' => env('TECHSCODE_MAVEN_PASSWORD'),
        ],
        'auth' => [
            'token' => env('TECHSCODE_AUTH_TOKEN'),
        ],
        'api' => [
            'github' => env('TECHSCODE_GITHUB_API_TOKEN'),
            'plugin' => env('TECHSCODE_PLUGIN_API_TOKEN'),
        ],
        'role_ids' => [
            'verified' => '416174015141642240',
            'patreon' => '795101981051977788',
            'patreon_adventurer' => '1089858831451951144',
            'patreon_pioneer' => '1089858895998091295',
            'patreon_coding_wizard' => '1089858921281359962',
            'support' => '1035905910511509585',
            'development' => '1035906012068200559',
            'leadership' => '1099678963778981968',
            'marketing' => '1035906095895556157',

            'ultrapermissions' => '416194311080771596',
            'ultraeconomy' => '749034791936196649',
            'ultramotd' => '936284238519599104',
            'ultrapunishments' => '531255363505487872',
            'ultraregions' => '465975554101739520',
            'ultracustomizer' => '416194287567372298',
            'ultrascoreboards' => '811397836616630352',
            'insanevaults' => '1057876528907694140',
            'insaneshops' => '576739274297442325',
            'insaneannouncer' => '1084143858352402462',
            'techseditor' => '1057876510125604994',
            'ultraeconomytest' => '1057876488684326922',
        ],
    ],

    'update_server_enabled' => env('UPDATE_SERVER_ENABLED', false),

];
