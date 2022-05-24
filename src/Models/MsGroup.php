<?php

namespace plokko\MsGraph\Models;

use \MsGraph;

/**
 * @property-read string $id
 * @property-read string $displayName
 * @property-read string $description
 * @property-read string $mail
 * @property-read string $createdDateTime
 */
class MsGroup extends \Microsoft\Graph\Model\Group
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
            case 'description':
                return $this->getDescription();
            case 'displayName':
            case 'display_name':
                return $this->getDisplayName();
            case 'mail':
                return $this->getMail();
            case 'createdDateTime':
            case 'created_datetime':
            case 'created_date_time':
                return $this->getCreatedDateTime();
            default:
        }
    }
}
