<?php
/**
 * Microsoft Graph settings
 * @see https://docs.microsoft.com/en-us/graph/tutorials/php?tutorial-step=3
 */
return [
    'tenant' => env('MSGRAPH_TENANT', 'common'),
    'clientId' => env('MSGRAPH_CLIENT_ID', ''),
    'clientSecret' => env('MSGRAPH_CLIENT_SECRET', ''),

    // User login callback
    'redirectUri' => strpos(env('MSGRAPH_REDIRECT_URI', ''),'//')===false?url(env('MSGRAPH_REDIRECT_URI', '/auth/callback')):env('MSGRAPH_REDIRECT_URI'),

    //Scope
    'scopes' => env('MSGRAPH_OAUTH_SCOPES', 'openid profile offline_access user.read mailboxsettings.read calendars.readwrite'),

    // URI to redirect after login
    'redirect_login' => '/',

    // Graph API 'v1.0' or 'beta', leave null for default
    'apiVersion' => null,

    //// Cache used for OAuth caching
    // Cache driver
    'cache_driver' => 'file',
    // Cache prefix
    'cache_prefix' => 'ms-graph',
];
