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

class MsUserOAuth extends MsOAuth
{

    private
        array $oauth_opt,
        $cachePrefix,
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
