<?php

namespace plokko\MsGraph\Models;

class MsDrive extends \Microsoft\Graph\Model\Drive
{

    function getSpecialItem($type){
        return \MsGraph::Drive()->getSpecialItem($this,$type);
    }

    function recordings(){
        return $this->listSpecialItems('recordings');
    }
}
