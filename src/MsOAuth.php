<?php

namespace plokko\MsGraph;

use Cache;
use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model\User as MsUser;
use plokko\MsGraph\Exceptions\InvalidAuthState;
use plokko\MsGraph\Exceptions\LoginException;

class MsOAuth
{

    private array
        $oauth_opt,
        $userFields = ['id','displayName','mail','mailboxSettings','userPrincipalName',];
    private
        $cachePrefix,
        $sessionPrefix='ms-graph-oauth',
        /**@var \Illuminate\Contracts\Cache\Repository **/
        $cache;

    public function __construct(array $oauth_opt)
    {
        $this->oauth_opt = $oauth_opt;
        $this->cachePrefix = config('ms-graph.cache_prefix','ms-graph');
        $this->cache = Cache::store(config('ms-graph.cache_driver','file'));
    }


    public function getOauthClient()
    {
        return new  GenericProvider($this->oauth_opt);
    }

    public function getAuthorizationUrl(){
        return $this->getOauthClient()->getAuthorizationUrl();
    }
    /**
     * Redirects to Microsoft login
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToAuthorizationUrl(){
        $oauthClient = $this->getOauthClient();
        $authUrl = $oauthClient->getAuthorizationUrl();

        // Save client state so we can validate in callback
        session([$this->sessionPrefix . '.oauthState' => $oauthClient->getState()]);

        // Redirect to AAD signin page
        return redirect()->away($authUrl);
    }

    /**
     * User login callback
     * @param Request $request
     * @throws InvalidAuthState
     * @throws LoginException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     * @return \Microsoft\Graph\Model\User|null
     */
    public function loginCallback(Request $request){
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

                ///--- Get logged user ---//
                $graph = new Graph();
                $graph->setAccessToken($accessToken->getToken());


                //TODO: check,
                $user = $graph->createRequest('GET', '/me?$select='.implode(',',$this->userFields))
                    ->setReturnType(MsUser::class)
                    ->execute();
                /**@var MsUser $user**/

                if($user->id){
                    //SAVE ACCESS TOKEN
                    $this->saveToken('id:'.$user->id,$accessToken);
                }
                return $user;
                ///---
            } catch (IdentityProviderException $e) {
                throw new LoginException('Error requesting access token', $e->getResponseBody());
            }
        }

        throw new LoginException($request->query('error'), $request->query('error_description'));
    }

    /**
     * @return AccessTokenInterface|null
     */
    public function getUserAccessToken($msId){
        //TODO
        return  $this->getToken('id:'.$id,function (){
             return null;//if not saved return null (need login)
        },true);
    }

    /**
     * @return AccessTokenInterface|null
     */
    public function getServerAccessToken(){
        return  $this->getToken('serverToken',function (){
            /// GET NEW SERVER TOKEN ///
            $oauthClient = $this->getOauthClient();
            try {
                // Try to get an access token using the client credentials grant.
                return $oauthClient->getAccessToken('client_credentials',[
                    'scope' => 'https://graph.microsoft.com/.default'
                ]);
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                // Failed to get the access token
                throw $e;
            }
        },true);
    }

    /**
     * @param string $scope
     * @param AccessTokenInterface $token
     * @return void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function saveToken($scope,AccessTokenInterface $token)
    {
        $this->cache->set($this->cachePrefix . '.' . $scope, $token);
    }
        /**
     * Get token from cache or refresh it
     * @param string $scope
     * @param Callable $onEmpty
     * @param boolean $autoRefresh Automatically refresh token if expired
     * @return AccessTokenInterface|null
     */
    protected function getToken($scope,Callable $onEmpty,$autoRefresh=true){
        $token = $this->cache->get($this->cachePrefix.'.'.$scope,$onEmpty);

        /**@var AccessTokenInterface $token**/
        if($token->hasExpired()){
            //Must refresh
            $token = $this->refreshToken($token,$scope,true);
        }

        return $token;
    }

    /**
     * Refresh token
     * @param AccessTokenInterface $token
     * @param string $scope
     * @param boolean $update update cache
     * @return AccessTokenInterface|null
     */
    protected function refreshToken(AccessTokenInterface $token,$scope,$update=true){
        try {
            $oauthClient = $this->getOauthClient();
            $newToken = $oauthClient->getAccessToken('refresh_token', [
                'refresh_token' => session('refreshToken')
            ]);

            if(!empty($scope) && $update){
                $this->cache->set($this->cachePrefix.'.'.$scope,$newToken);
            }
            return $newToken;
        }
        catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            //Token refresh failed
            return null;
        }
    }

    /**
     * Delete token data
     * @param string $scope
     * @return void
     */
    protected function clearToken($scope){
        $this->cache->forget($this->cachePrefix.'.'.$scope);
    }
}
