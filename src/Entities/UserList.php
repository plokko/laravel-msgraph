<?php

namespace plokko\MsGraph\Entities;

use plokko\MsGraph\MsGraph;
use \Microsoft\Graph\Model\User as MsUser;

class UserList extends BaseEntity
{
    protected $opts = [];

    /**
     * @param int $limit
     * @return $this
     */
    function take($limit = null)
    {
        $this->opts['limit'] = $limit;
        return $this;
    }

    /*
     * @param int $skip
     * @return $this
    function skip($skip=null){
        $this->opt['skip'] = $skip;
        return $this;
    }
     */


    function search($search = null)
    {
        $this->opts['search'] = '"' . $search . '"';
        return $this;
    }

    function filter($filter)
    {
        $this->opts['filter'] = $filter;
        return $this;
    }

    function filterNotGuests(){
        return $this->filter("userType ne 'guest'");
    }

    function orderBy($field = null)
    {
        $this->opts['orderBy'] = $field;
    }

    /**
     * @param string[]|null $select fields to select
     * @return void
     */
    function select(array $select = null)
    {
        $this->opts['select'] = $select;
    }

    protected function buildUri($action = '')
    {
        $eventual = false;
        $opt = [];

        foreach ([
                     'limit' => '$top',
                     'filter' => '$top',
                     'search' => '$search',
                     'orderBy' => '$orderBy',

                 ] as $k => $v) {
            if (!empty($this->opts[$k])) {
                $opt[$v] = $this->opts[$k];
            }

            if ($k === 'search')
                $eventual = true;
        }

        if ($action === '$count')
            $eventual = true;

        if (!empty($this->opts['select'])) {
            $opt['$select'] = implode(',', $this->opts['select']);
        }
        if (!empty($this->opts['filter'])) {
            $opt['$filter'] = $this->opts['filter'];
        }
        //dd("/users/$action?".http_build_query($opt));
        return [
            'uri' => "/users/$action?" . http_build_query($opt),
            'eventual' => $eventual
        ];
    }

    protected function getQuery($action=''){
        $uri = $this->buildUri($action);
        $q = $this->msGraph->graph()->createRequest('GET',$uri['uri']);
        if($uri['eventual']){
            $q->addHeaders(["consistencylevel" => "eventual"]);
        }

        return $q;
    }

    /**
     * @return \Microsoft\Graph\Model\User[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function get()
    {
        $r = $this->getQuery()
                ->setReturnType(MsUser::class)
                ->execute();

        return $r;
    }


    /**
     * Count users
     * @return integer
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    function count()
    {
        $r = $this->getQuery('$count')->execute();
        return $r->getBody();
    }

}
