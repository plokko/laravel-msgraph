<?php

namespace plokko\MsGraph;

use Illuminate\Http\Request;
use \League\OAuth2\Client\Provider\GenericProvider;
use \League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model\User as MsUser;
use plokko\MsGraph\Entities\User;
use plokko\MsGraph\Exceptions\InvalidAuthState;
use plokko\MsGraph\Exceptions\LoginException;

class MsGraph
{

    /**@var array */
    private $oauth_opt;

    private $sessionPrefix = 'msgraph';

    function __construct(array $oauth_opt = null)
    {
        $authority = 'https://login.microsoftonline.com/'.config('ms-graph.tenant');
        $this->oauth_opt = $oauth_opt ?? [
                'clientId' => config('ms-graph.clientId'),
                'clientSecret' => config('ms-graph.clientSecret'),
                'redirectUri' => config('ms-graph.redirectUri'),
                'urlAuthorize' => $authority. '/oauth2/v2.0/authorize',
                'urlAccessToken' => $authority .'/oauth2/v2.0/token',
                'urlResourceOwnerDetails' => '',
                'scopes' => config('ms-graph.scopes'),
            ];
    }

    protected function getOauthClient()
    {
        return new  GenericProvider($this->oauth_opt);
    }

    /**
     * Redirects to Microsoft login
     */
    public function redirectToLogin()
    {
        // Initialize the OAuth client
        $oauthClient = $this->getOauthClient();

        $authUrl = $oauthClient->getAuthorizationUrl();

        // Save client state so we can validate in callback
        session([$this->sessionPrefix . '.oauthState' => $oauthClient->getState()]);

        // Redirect to AAD signin page
        return redirect()->away($authUrl);
    }


    /**
     * @param Request $request
     * @throws LoginException
     */
    public function loginCallback(Request $request = null)
    {
        if (!$request)
            $request = request();
        // Validate state
        $expectedState = session($this->sessionPrefix . '.oauthState');
        $request->session()->forget($this->sessionPrefix . '.oauthState');
        $providedState = $request->query('state');

        if (!isset($expectedState)) {
            // If there is no expected state in the session,
            // do nothing and redirect to the home page.
            return null;
            //return redirect(config('ms-graph.redirect_login'));
        }

        if (!isset($providedState) || $expectedState != $providedState) {
            throw new InvalidAuthState();
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
                $this->onAccessToken($accessToken);
            } catch (IdentityProviderException $e) {
                return redirect('/')
                    ->with('error', 'Error requesting access token')
                    ->with('errorDetail', json_encode($e->getResponseBody()));
            }
        }

        throw new LoginException($request->query('error'), $request->query('error_description'));
    }


    protected function onAccessToken(AccessTokenInterface $accessToken)
    {
        // TODO: TEMPORARY FOR TESTING!
        abort(501, 'Access token received:' . $accessToken->getToken());

        $graph = new Graph();
        $graph->setAccessToken($accessToken->getToken());

        $user = $graph->createRequest('GET', '/me?$select=displayName,mail,mailboxSettings,userPrincipalName')
            ->setReturnType(MsUser::class)
            ->execute();

        //TODO
        dd($accessToken, $user);
    }

    protected function getServerAccessToken(){
        //TODO: cache, refresh
        $oauthClient = $this->getOauthClient();
        try {
            // Try to get an access token using the client credentials grant.
            return $oauthClient->getAccessToken('client_credentials',[
                'scope' => 'https://graph.microsoft.com/.default'
            ]);
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            // Failed to get the access token
            exit($e->getMessage());
        }
    }
    function graph(){

        $graph = new Graph();
        //$graph->setBaseUrl("https://graph.microsoft.com/")
        if(config('ms-graph.apiVersion')){
            $graph->setApiVersion(config('ms-graph.apiVersion'));
        }
        $graph->setAccessToken($this->getServerAccessToken());
        return $graph;
    }


    function User(){
        return new User($this);
    }
}
