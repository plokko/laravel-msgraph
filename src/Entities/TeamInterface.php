<?php

namespace plokko\MsGraph\Entities;

use plokko\MsGraph\Models\MsChannel;

class TeamInterface extends BaseEntity
{

    /**
     * @param \Microsoft\Graph\Model\Team|\Microsoft\Graph\Model\Group|string $team
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     * @return MsChannel[]
     */
    function getChannels($team){
        $team_id= $drive instanceof \Microsoft\Graph\Model\Entity ? $team->getId():$team;

        return $this->msGraph->graph()
            ->createRequest('get',"/teams/$team_id/channels")
            ->setReturnType(MsChannel::class)
            ->execute();
    }

    /**
     * @param \Microsoft\Graph\Model\Team|\Microsoft\Graph\Model\Group|string $team
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     * @return MsChannel
     */
    function getPrimaryChannel($team){
        $team_id= $drive instanceof \Microsoft\Graph\Model\Entity ? $team->getId():$team;

        return $this->msGraph->graph()
            ->createRequest('get',"/teams/$team_id/primaryChannel")
            ->setReturnType(MsChannel::class)
            ->execute();
    }

}
