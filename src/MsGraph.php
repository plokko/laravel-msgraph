<?php

namespace plokko\MsGraph;

use Illuminate\Http\Request;
use \League\OAuth2\Client\Provider\GenericProvider;
use \League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model\User as MsUser;
use plokko\MsGraph\Entities\User;
use plokko\MsGraph\Entities\UserAuth;
use plokko\MsGraph\Exceptions\InvalidAuthState;
use plokko\MsGraph\Exceptions\LoginException;

class MsGraph implements MsGraphAuthInterface
{
    private MsOAuth $oauth;

    function __construct(array $oauth_opt = null)
    {
        $authority = 'https://login.microsoftonline.com/'.config('ms-graph.tenant');
        $redirectUri = config('ms-graph.redirectUri');

        $this->oauth = new MsOauth($oauth_opt ?? [
                'clientId' => config('ms-graph.clientId'),
                'clientSecret' => config('ms-graph.clientSecret'),
                'redirectUri' => $redirectUri,
                'urlAuthorize' => $authority. '/oauth2/v2.0/authorize',
                'urlAccessToken' => $authority .'/oauth2/v2.0/token',
                'urlResourceOwnerDetails' => '',
                'scopes' => config('ms-graph.scopes'),
            ]);
    }

    /**
     * Get Microsoft Graph instance
     * @return Graph
     */
    function graph(){
        $graph = new Graph();
        //$graph->setBaseUrl("https://graph.microsoft.com/")
        if(config('ms-graph.apiVersion')){
            $graph->setApiVersion(config('ms-graph.apiVersion'));
        }
        $graph->setAccessToken($this->oauth->getServerAccessToken());
        return $graph;
    }


    /**
     * @return User
     */
    function User(){
        return new User($this);
    }

    /**
     * @return UserAuth
     */
    function Auth(){
        return new UserAuth($this,$this->oauth);
    }
}
