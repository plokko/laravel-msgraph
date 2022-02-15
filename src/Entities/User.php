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

    /**
     * Create a new user
     * @param \Microsoft\Graph\Model\User|array $user
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     * @return \Microsoft\Graph\Model\User Created user
     */
    function create($user,$password=null,$accountEnabled=null,$forcePasswordChange=false){
        $data = null;
        if(is_array($user)){
            $data = $user;
        }elseif($user instanceof \Microsoft\Graph\Model\User){
            $data = $user->getProperties();
        }else{
            throw new \UnexpectedValueException('Unexpected parameter');
        }

        ////
        if(empty($data['accountEnabled']) || $accountEnabled!==null){
            $data['accountEnabled'] = $accountEnabled!==false;
        }

        if(empty($data['password'])||$password!==null){
            if($password!==null)
                $data['password'] = $password;
        }

        if($forcePasswordChange){
            $data['passwordProfile']=[
                'forceChangePasswordNextSignIn' => $forcePasswordChange,
            ];
            if(isset($data['password']))
                $data['passwordProfile']['password'] = $data['password'];
            unset($data['password']);
        }

        ////
        $missingFields = [];
        foreach(['accountEnabled','displayName','mailNickname','userPrincipalName'] AS $k){
            if(!isset($data[$k])|| $data[$k]===null || $data[$k]==='')
                $missingFields[] = $k;
        }
        if(empty($data['password']) && empty($data['passwordProfile']) ){
            $missingFields[] = 'password';
        }
        if(count($missingFields)>0){
            throw new \UnexpectedValueException('Missing required fields: '.implode(',',$missingFields));
        }

        ////
        $q = $this->msGraph->graph()
            ->createRequest('POST','/users')
            ->attachBody($data)
            ->setReturnType(MsUser::class)
            ->execute()
            ;

        return $q;
    }

    /**
     * @param \Microsoft\Graph\Model\User|array $user
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    function update($user){
        $data = null;
        if(is_array($user)){
            $data = $user;
        }elseif($user instanceof \Microsoft\Graph\Model\User){
            $data = $user->getProperties();
        }else{
            throw new \UnexpectedValueException('Unexpected parameter');
        }
        ////
        $cnd = '';
        if(!empty($data['id'])){
            $cnd='/'.urlencode($data['id']);
        }elseif(!empty($data['userPrincipalName'])){
            $cnd='(\''.urlencode($data['userPrincipalName']).'\')';
        }else{
            throw new \UnexpectedValueException('Fields id or userPrincipalName are required.');
        }

        ////
        return $this->msGraph->graph()
            ->createRequest('PATCH','/users'.$cnd)
            ->attachBody($data)
            ->execute();
    }


    function delete($id){
        return $this->msGraph->graph()
            ->createRequest('DELETE','/users/'.str_replace(['/','$','?',''],'',$id))
            ->execute();
    }

    /**
     * @param string $principalName
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    function deleteByPrincipalName($principalName){
        return $this->msGraph->graph()
                ->createRequest('DELETE','/users(\''.urlencode($principalName).'\')')
                ->execute();
    }
}
