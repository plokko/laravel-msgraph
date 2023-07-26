<?php
namespace plokko\MsGraph\Models;

use Microsoft\Graph\Model\Calendar;
use \MsGraph;

/**
 * @property-read string|null $accountName
 * @property-read string|null $accountId
 * @property-read string|null $appliesTo
 * @property-read string|null $capabilityStatus
 * @property-read boolean $enabled
 * @property-read integer|null $consumedUnits
 * @property-read string|null $id
 * @property-read string|null $skuId
 * @property-read string|null $skuPartNumber
 * @property-read string[]|null $subscriptionIds
 * @property-read \Microsoft\Graph\Model\LicenseUnitsDetail|null $prepaidUnits
 * @property-read array|null $prepaidUnits
 */
class MsSubscribedSku extends \Microsoft\Graph\Model\SubscribedSku
{
    function __get($k)
    {
        switch ($k) {
            case 'accountName':
                return $this->getAccountName();
            case 'accountId':
                return $this->getAccountId();
            case 'appliesTo':
                return $this->getAppliesTo();
            case 'consumedUnits':
                return $this->getConsumedUnits();
            case 'capabilityStatus':
                return $this->getCapabilityStatus();
            case 'id':
                return $this->getId();
            case 'skuId':
                return $this->getSkuId();
            case 'skuPartNumber':
                return $this->getSkuPartNumber();
            case 'subscriptionIds':
                return $this->getSubscriptionIds();
            case 'prepaidUnits':
                return $this->getPrepaidUnits();
            case 'servicePlans':
                return $this->getServicePlans();

            case 'enabled':
                return in_array($this->getCapabilityStatus(), ['Enabled', 'Warning']);

            default:
        }
    }
}
