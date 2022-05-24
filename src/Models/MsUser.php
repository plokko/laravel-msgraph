<?php

namespace plokko\MsGraph\Models;

use Microsoft\Graph\Model\Calendar;
use \MsGraph;

/**
 * @property-read string $id
 * @property-read string $display_name
 * @property-read string $user_principal_name
 */
class MsUser extends \Microsoft\Graph\Model\User
{

    public function listUserCalendars(){
        return MsGraph::User()->listCalendars($this);
    }

    public function getUserCalendar($calendar){
        return MsGraph::User()->getCalendar($calendar);
    }

    /**
     * @return MsDrive|null
     */
    function drive(){
        return MsGraph::User()->getDrive($this);
    }

    /**
     * @param $user
     * @return MsDrive[]|null
     */
    function drives($user){
        return MsGraph::User()->getDrives($this);
    }


    function __get($k){
        switch($k){
            case 'id':
                return $this->getId();
            case 'display_name':
                return $this->getDisplayName();
            case 'principal_name':
            case 'user_principal_name':
                return $this->getUserPrincipalName();
            default:
        }
    }
}