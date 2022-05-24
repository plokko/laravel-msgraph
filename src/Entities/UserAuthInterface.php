<?php

namespace plokko\MsGraph\Entities;

use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Microsoft\Graph\Graph;
//use Microsoft\Graph\Model\User as MsUser;
use plokko\MsGraph\Models\MsUser;
use plokko\MsGraph\Exceptions\InvalidAuthState;
use plokko\MsGraph\Exceptions\LoginException;
use plokko\MsGraph\MsGraph;
use plokko\MsGraph\MsOAuth;

class UserAuthInterface extends BaseEntity
{
    private
        MsOAuth $oauth;
    private
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

    public function redirectToSelectUser()
    {
        return $this->oauth->redirectToAuthorizationUrl([
            'prompt'=> 'select_account', //login, none, select_account, and consent.
        ]);
    }

    public function getAuthorizationUrl(){
        return $this->oauth->getAuthorizationUrl();
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

    /**
     * Return the access token given a Microsoft User
     * @param Microsoft\Graph\Model\User $msUser
     * @return AccessTokenInterface|null Client token or null if not present
     */
    public function getUserAccessToken(MsUser $msUser){
        return $this->oauth->getUserAccessToken($msUser->getId());
    }
}
