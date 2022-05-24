<?php

namespace plokko\MsGraph\Models;

use MsGraph;

/**
 * @property-read string $id
 * @property-read string $name
 * @property-read array|null $owner
 */
class MsCalendar extends \Microsoft\Graph\Model\Calendar
{

    /**
     * @param Microsoft\Graph\Model\User|string $user
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function listUserEvents($user){
        MsGraph::User()->listCalendarEvents($user);
    }


    function __get($k){
        switch($k){
            case 'id':
                return $this->getId();
            case 'name':
                return $this->getName();
            case 'owner':
                return $this->getOwner();

        }
    }
}
