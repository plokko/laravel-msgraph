<?php

namespace plokko\MsGraph\Entities;

use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model\User as MsUser;
use plokko\MsGraph\Exceptions\InvalidAuthState;
use plokko\MsGraph\Exceptions\LoginException;
use plokko\MsGraph\MsGraph;
use plokko\MsGraph\MsOAuth;

class UserAuth extends BaseEntity
{

    private
        MsOAuth $oauth,
        $sessionPrefix='ms-oauth';

    function __construct(MsGraph $instance,MsOAuth $oauth)
    {
        parent::__construct($instance);
        $this->oauth=$oauth;
    }

    /**
     * Redirects to Microsoft login
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToLogin()
    {
        return $this->oauth->redirectToAuthorizationUrl();
    }


    /**
     * @param Request $request
     * @throws LoginException
     * @return \Microsoft\Graph\Model\User|null
     */
    public function loginCallback(Request $request = null)
    {
        return $this->oauth->loginCallback($request);
    }


    protected function storeAccessToken(){

    }
}
