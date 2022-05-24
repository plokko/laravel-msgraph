<?php

namespace plokko\MsGraph\Models;

use \MsGraph;

/**
 * @property-read string $id
 * @property-read string $displayName
 * @property-read string $description
 * @property-read string $mail
 * @property-read string $createdDateTime
 * @property-read string $webUrl
 */
class MsChannel extends \Microsoft\Graph\Model\Channel
{
    function __get($k){
        switch($k){
            case 'id':
                return $this->getId();
            case 'webUrl':
                return $this->getWebUrl();
            case 'description':
                return $this->getDescription();
            case 'displayName':
            case 'display_name':
                return $this->getDisplayName();
            case 'mail':
                return $this->getMail();
            case 'createdDateTime':
            case 'created_datetime':
            case 'created_date_time':
                return $this->getCreatedDateTime();
            default:
        }
    }
}
