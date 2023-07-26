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

    public function listUserCalendars()
    {
        return MsGraph::User()->listCalendars($this);
    }

    public function getUserCalendar($calendar)
    {
        return MsGraph::User()->getCalendar($calendar);
    }

    /**
     * @return MsDrive|null
     */
    function drive()
    {
        return MsGraph::User()->getDrive($this);
    }

    /**
     * @return MsDrive[]|null
     */
    function drives()
    {
        return MsGraph::User()->getDrives($this);
    }

    /**
     * @param \Microsoft\Graph\Model\Group|string $group
     * @return \Microsoft\Graph\Http\GraphResponse|mixed
     */
    function addToGroup($group)
    {
        return \MsGraph::Group()->addMember($group, $this);
    }
    /**
     * @param \Microsoft\Graph\Model\Group|string $group
     * @return \Microsoft\Graph\Http\GraphResponse|mixed
     */
    function removeFromGroup($group)
    {
        return \MsGraph::Group()->removeMember($group, $this);
    }

    /**
     * @return \Microsoft\Graph\Model\Group[]
     */
    function memberOfGroups()
    {
        return \MsGraph::Group()->getMemberOf($this);
    }

    /**
     * @return \Microsoft\Graph\Model\Group[]
     */
    function transitiveMemberOfGroups()
    {
        return \MsGraph::Group()->getTransitiveMemberOf($this);
    }

    /**
     * @return \Microsoft\Graph\Model\Team[]
     */
    function joinedTeams()
    {
        return \MsGraph::Group()->getJoinedTeams($this);
    }

    function listLicenses()
    {
        return \MsGraph::User()->listLicenses($this);
    }

    function addLicenses(array $licenses)
    {
        return \MsGraph::User()->assignLicense($this, $licenses, []);
    }
    function removeLicenses(array $licenses)
    {
        return \MsGraph::User()->assignLicense($this, [], $licenses);
    }
    function assignLicenses(array $add, array $remove)
    {
        return \MsGraph::User()->assignLicense($this, $add, $remove);
    }


    function update(array $data){
        $data['id'] = $this->getId();
        $data['userPrincipalName'] = $this->getUserPrincipalName();

        return \MsGraph::User()->update($data);
    }

    function __get($k)
    {
        switch ($k) {
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
