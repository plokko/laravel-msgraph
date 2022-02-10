<?php

namespace plokko\MsGraph\Entities;

use GuzzleHttp\Exception\RequestException;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Http\GraphResponse;
use plokko\MsGraph\MsGraph;
use \Microsoft\Graph\Model\User as MsUser;

class User extends BaseEntity{

    /**
     * @return UserList
     */
    function list(){
        return new UserList($this->msGraph);
    }


    /**
     * Create a new user
     * @param \Microsoft\Graph\Model\User $user
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    function create(MsUser $user,$password=null,$accountEnabled=null,$forcePasswordChange=true){
        $data = $user->getProperties();
        if(empty($data['accountEnabled'])||$accountEnabled!==null){
            $data['accountEnabled'] = $accountEnabled!==false;
        }
        if(empty($data['password'])||$password!==null){
            $data['password'] = $password;
        }

        if($forcePasswordChange){
            $data['passwordProfile']=[
                'forceChangePasswordNextSignIn' => $forcePasswordChange,
                'password' => $data['password'],
            ];
            unset($data['password']);
        }
        /*
         required fields:
        "accountEnabled": true,
          "displayName": "Adele Vance",
          "mailNickname": "AdeleV",
          "userPrincipalName": "AdeleV@contoso.onmicrosoft.com",
          "passwordProfile" : {
            "forceChangePasswordNextSignIn": true,
            "password": "xWwvJ]6NMw+bWH-d"
          }
         */

        $q = $this->msGraph->graph()
            ->createRequest('POST','/users')
            ->attachBody($data)
            ->execute()
            ;

        return $q;
    }

    /**
     * Get an user by id
     * @param string $id User id ex: "87d349ed-44d7-43e1-9a83-5f2406dee5bd"
     * @return GraphResponse|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     * @return \Microsoft\Graph\Model\User|null
     */
    function find($id){
        try{
            return $this->msGraph->graph()
                ->createRequest('GET','/users/'.str_replace(['/','$','?',''],'',$id))
                ->setReturnType(MsUser::class)
                ->execute();
        } catch(RequestException $ex){
            if($ex->getCode()==404){
                return null;
            }
            throw $ex;
        }
    }

    /**
     * Get an user by principal name
     * @param string $name User principalName ex: "AdeleVance@contoso.com"
     * @return GraphResponse|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     * @return \Microsoft\Graph\Model\User|null
     */
    function findByPrincipalName($name){
        try{
            return $this->msGraph->graph()
                ->createRequest('GET','/users(\''.urlencode($name).'\')')
                ->setReturnType(MsUser::class)
                ->execute();
        } catch(RequestException $ex){
            if($ex->getCode()==404){
                return null;
            }
            throw $ex;
        }
    }

}
