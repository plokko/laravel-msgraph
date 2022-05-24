<?php

namespace plokko\MsGraph\Entities;

class DriveInterface extends BaseEntity
{

    /**
     * @param \Microsoft\Graph\Model\Drive|string $drive
     * @return \Microsoft\Graph\Model\DriveItem[]
     */
    function listRootItems($drive){
        $drive_id= $drive instanceof \Microsoft\Graph\Model\Drive ? $drive->getId():$drive;

        return $this->msGraph->graph()->createRequest('get',"/drives/$drive_id/root/children")
            ->setReturnType(\Microsoft\Graph\Model\DriveItem::class)
            ->execute();
    }
    /**
     * @param \Microsoft\Graph\Model\Drive|string $drive
     * @param string $type type of element (recordings,documents,photos,cameraroll,approot,music)
     * @return \Microsoft\Graph\Model\DriveItem[]
     */
    function getSpecialItem($drive,$type){
        $drive_id= $drive instanceof \Microsoft\Graph\Model\Drive ? $drive->getId():$drive;

        return $this->msGraph->graph()->createRequest('get',"/drives/$drive_id/special/$type")
            ->setReturnType(\Microsoft\Graph\Model\DriveItem::class)
            ->execute();
    }

}
