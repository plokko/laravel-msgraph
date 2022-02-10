<?php

namespace plokko\MsGraph\Entities;

use Microsoft\Graph\Graph;
use Microsoft\Graph\Http\GraphResponse;
use plokko\MsGraph\MsGraph;

class User extends BaseEntity{

    /**
     * @return UserList
     */
    function list(){
        return new UserList($this->msGraph);
    }


}
