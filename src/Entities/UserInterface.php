<?php

namespace plokko\MsGraph\Entities;

use GuzzleHttp\Exception\RequestException;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Http\GraphResponse;
use Microsoft\Graph\Model\Calendar;
use Microsoft\Graph\Model\Team;
use plokko\MsGraph\Models\MsCalendar;
use plokko\MsGraph\Models\MsUser;
use plokko\MsGraph\Models\MsDrive;

class UserInterface extends BaseEntity{

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
     * @return MsUser|null
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
     * List user calendars
     * @param MsUser|string $user Microsoft user or id
     * @return MsCalendar[]
     */
    function listCalendars($user){
        $user_id = $user instanceof \Microsoft\Graph\Model\User? $user->getId():$user;

        return $this->msGraph->graph()
            ->createRequest('get',"/users/$user_id/calendars")
            ->setReturnType(MsCalendar::class)
            ->execute();
    }

    /**
     * Get a specific user calendar
     * @param Calendar|string $calendar
     * @return MsCalendar
     */
    function getCalendar($calendar){
        $calendar_id = ($calendar && $calendar instanceof Calendar)?$calendar->getId():$calendar;
        return $this->msGraph->graph()
            ->createRequest('get',$calendar_id?"/users/$user_id/calendars/$calendar_id":"/users/$user_id/calendar")
            ->setReturnType(MsCalendar::class)
            ->execute();
    }

    /**
     * @param MsUser|string $user
     * @param Calendar|string|null $calendar
     * @return GraphResponse|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    function listCalendarEvents($user,$calendar=null){
        $user_id = $user instanceof \Microsoft\Graph\Model\User? $user->getId():$user;
        $calendar_id = ($calendar && $calendar instanceof Calendar)?$calendar->getId():$calendar;

        return $this->msGraph->graph()
            ->createRequest('get',$calendar_id?"/users/$user_id/calendars/$calendar_id/events":"/users/$user_id/calendar/events")
            ->setReturnType(MsCalendar::class)
            ->execute();
    }

    /**
     * Get an user by principal name
     * @param string $name User principalName ex: "AdeleVance@contoso.com"
     * @return GraphResponse|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     * @return MsUser|null
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
     * @return MsUser Created user
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
                'forceChangePasswordNextSignIn' => !!$forcePasswordChange,
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


    /**
     * @param MsUser|string $user Microsoft user or id
     * @return \plokko\MsGraph\Models\MsDrive
     */
    function getDrive($user){
        $user_id = $user instanceof \Microsoft\Graph\Model\User? $user->getId():$user;

        return $this->msGraph->graph()
            ->createRequest('get',"/users/$user_id/drive")
            ->setReturnType(MsDrive::class)
            ->execute();
    }

    /**
     * @param MsUser|string $user Microsoft user or id
     * @return \plokko\MsGraph\Models\MsDrive[]
     */
    function getDrives($user){
        $user_id = $user instanceof \Microsoft\Graph\Model\User? $user->getId():$user;

        return $this->msGraph->graph()
            ->createRequest('get',"/users/$user_id/drives")
            ->setReturnType(MsDrive::class)
            ->execute();
    }

    /**
     * @param MsUser|string $user Microsoft user or id
     * @return Group[]
     */
    function getMemberOf($user){
        $user_id = $user instanceof \Microsoft\Graph\Model\User? $user->getId():$user;

        return $this->msGraph->graph()
            ->createRequest('get',"/users/$user_id/memberOf")
            ->setReturnType(\Microsoft\Graph\Model\Group::class)
            ->execute();
    }

    /**
     * @param MsUser|string $user Microsoft user or id
     * @return Group[]
     */
    function getTransitiveMemberOf($user){
        $user_id = $user instanceof \Microsoft\Graph\Model\User? $user->getId():$user;

        return $this->msGraph->graph()
            ->createRequest('get',"/users/$user_id/transitiveMemberOf")
            ->setReturnType(\Microsoft\Graph\Model\Group::class)
            ->execute();
    }

    /**
     * @param \Microsoft\Graph\Model\User|string $user Microsoft user or id
     * @return Team[]
     */
    function getJoinedTeams($user){
        $user_id = $user instanceof \Microsoft\Graph\Model\User? $user->getId():$user;

        return $this->msGraph->graph()
            ->createRequest('get',"/users/$user_id/joinedTeams")
            ->setReturnType(\Microsoft\Graph\Model\Team::class)
            ->execute();
    }

    /**
     * @param \Microsoft\Graph\Model\Group|string $group
     * @param \Microsoft\Graph\Model\User|string $user Microsoft user or id
     **/
    function addToGroup($user,$group){
        return \MsGraph::Group()->addMember($group,$user);
    }

    /**
     * @param \Microsoft\Graph\Model\Group|string $group
     * @param \Microsoft\Graph\Model\User|string $user Microsoft user or id
     **/
    function removeFromGroup($user,$group){
        return \MsGraph::Group()->removeMember($group,$user);
    }
}
