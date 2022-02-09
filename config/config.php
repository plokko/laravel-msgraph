<?php
/**
 * Microsoft Graph settings
 * @see https://docs.microsoft.com/en-us/graph/tutorials/php?tutorial-step=3
 */
return [
  'tenant'            => env('MSGRAPH_OAUTH_TENANT', 'common'),
  'appId'             => env('MSGRAPH_OAUTH_APP_ID', ''),
  'appSecret'         => env('MSGRAPH_OAUTH_APP_SECRET', ''),
  'redirectUri'       => env('MSGRAPH_OAUTH_REDIRECT_URI', ''),
  'scopes'            => env('MSGRAPH_OAUTH_SCOPES', ''),
  'authority'         => env('MSGRAPH_OAUTH_AUTHORITY', 'https://login.microsoftonline.com/'.env('MSGRAPH_OAUTH_TENANT', 'common')),
  'authorizeEndpoint' => env('MSGRAPH_OAUTH_AUTHORIZE_ENDPOINT', '/oauth2/v2.0/authorize'),
  'tokenEndpoint'     => env('MSGRAPH_OAUTH_TOKEN_ENDPOINT', '/oauth2/v2.0/token'),

  ///
  'redirect_login'    => '/',
];
