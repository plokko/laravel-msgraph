<?php
namespace plokko\MsGraph\Facades;

use Illuminate\Support\Facades\Facade;

class MsGraph extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \plokko\MsGraph\MsGraph::class;
    }
}