<?php

namespace plokko\MsGraph\Entities;

use plokko\MsGraph\MsGraph;

abstract class BaseEntity
{
    protected MsGraph $msGraph;
    function __construct(MsGraph $instance)
    {
        $this->msGraph = $instance;
    }
}
