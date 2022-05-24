<?php

namespace plokko\MsGraph\Entities;

use plokko\MsGraph\Models\MsDrive;
use plokko\MsGraph\Models\MsGroup;
use plokko\MsGraph\Models\MsUser;

/**
 * @see https://docs.microsoft.com/en-us/graph/api/resources/group?view=graph-rest-1.0
 */
class GroupInterface extends BaseEntity
{

    /**
     * @return MsGroup[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    function listAll(){
        return $this->msGraph->graph()
            ->createRequest('get',"/groups")
            ->setReturnType(MsGroup::class)
            ->execute();
    }

    /**
     * @param \Microsoft\Graph\Model\Group|string $group Microsoft group or id
     * @return \plokko\MsGraph\Models\MsDrive
     */
    function getDrive($group){
        $group_id = $group instanceof \Microsoft\Graph\Model\Group ? $group->getId():$group;

        return $this->msGraph->graph()
            ->createRequest('get',"/groups/$group_id/drive")
            ->setReturnType(MsDrive::class)
            ->execute();
    }

    /**
     * @param \Microsoft\Graph\Model\Group|string $user Microsoft user or id
     * @return \plokko\MsGraph\Models\MsDrive[]
     */
    function getDrives($group){
        $group_id = $group instanceof \Microsoft\Graph\Model\Group ? $group->getId():$group;

        return $this->msGraph->graph()
            ->createRequest('get',"/groups/$group_id/drives")
            ->setReturnType(MsDrive::class)
            ->execute();
    }

    /**
     * @param \Microsoft\Graph\Model\Group|string $group
     * @param string $type type of element (recordings,documents,photos,cameraroll,approot,music)
     * @return \Microsoft\Graph\Model\DriveItem[]
     */
    function getSpecialItem($group,$type){
        $group_id = $group instanceof \Microsoft\Graph\Model\Group ? $group->getId():$group;

        return $this->msGraph->graph()->createRequest('get',"/groups/$group_id/special/$type")
            ->setReturnType(\Microsoft\Graph\Model\DriveItem::class)
            ->execute();
    }
    /**
     * @param string $group_id Group id
     * @return MsGroup|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    function get($group){
        $group_id = $group instanceof \Microsoft\Graph\Model\Group ? $group->getId():$group;
        return $this->msGraph->graph()
            ->createRequest('get',"/groups/$group_id")
            ->setReturnType(MsGroup::class)
            ->execute();
    }


    /**
     * @param \Microsoft\Graph\Model\Group|string $group
     * @param \Microsoft\Graph\Model\User|string $user Microsoft user or id to add
     **/
    function addMember($group,$user){
        $group_id = $group instanceof \Microsoft\Graph\Model\Group ? $group->getId():$group;
        $user_id = $user instanceof \Microsoft\Graph\Model\User? $user->getId():$user;
        //POST /groups/{group-id}/members/$ref

        return $this->msGraph->graph()
            ->createRequest('post',"/groups/$group_id/members/\$ref")
            ->attachBody([
                '@odata.id' => "https://graph.microsoft.com/v1.0/users/$user_id"
            ])

            //->setReturnType(\Microsoft\Graph\Model\Team::class)
            ->execute();
    }
}
