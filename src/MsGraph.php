<?php
namespace plokko\MsGraph;

use \League\OAuth2\Client\Provider\GenericProvider;

class MsGraph{

    /**@var array */
    private $oauth_opt;

    function __construct(array $oauth_opt=null)
    {
        
        $this->oauth_opt = $oauth_opt??[
            'clientId'                => config('ms-graph.appId'),
            'clientSecret'            => config('ms-graph.appSecret'),
            'redirectUri'             => config('ms-graph.redirectUri'),
            'urlAuthorize'            => config('ms-graph.authority').config('ms-graph.authorizeEndpoint'),
            'urlAccessToken'          => config('ms-graph.authority').config('ms-graph.tokenEndpoint'),
            'urlResourceOwnerDetails' => '',
            'scopes'                  => config('ms-graph.scopes')
        ];
    }

    protected function getOauthClient(){
        return new  GenericProvider($this->oauth_opt);
    }

    /**
     * Redirects to Microsoft login
     */
    public function redirectToSignin()
    {
      // Initialize the OAuth client
      $oauthClient = $this->getOauthClient();
  
      $authUrl = $oauthClient->getAuthorizationUrl();
  
      // Save client state so we can validate in callback
      session(['oauthState' => $oauthClient->getState()]);
  
      // Redirect to AAD signin page
      return redirect()->away($authUrl);
    }
  
    public function callback(Request $request)
    {
      // Validate state
      $expectedState = session('oauthState');
      $request->session()->forget('oauthState');
      $providedState = $request->query('state');
  
      if (!isset($expectedState)) {
        // If there is no expected state in the session,
        // do nothing and redirect to the home page.
        return redirect(config('ms-graph.redirect_login'));
      }
  
      if (!isset($providedState) || $expectedState != $providedState) {
        return redirect(config('ms-graph.redirect_login'))
          ->with('error', 'Invalid auth state')
          ->with('errorDetail', 'The provided auth state did not match the expected value');
      }
  
      // Authorization code should be in the "code" query param
      $authCode = $request->query('code');
      if (isset($authCode)) {
        // Initialize the OAuth client
        $oauthClient = $this->getOauthClient();
  
        try {
          // Make the token request
          $accessToken = $oauthClient->getAccessToken('authorization_code', [
            'code' => $authCode
          ]);
  
          // TEMPORARY FOR TESTING!
          return redirect('/')
            ->with('error', 'Access token received')
            ->with('errorDetail', $accessToken->getToken());
        }
        catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
          return redirect('/')
            ->with('error', 'Error requesting access token')
            ->with('errorDetail', json_encode($e->getResponseBody()));
        }
      }
  
      return redirect('/')
        ->with('error', $request->query('error'))
        ->with('errorDetail', $request->query('error_description'));
    }
}