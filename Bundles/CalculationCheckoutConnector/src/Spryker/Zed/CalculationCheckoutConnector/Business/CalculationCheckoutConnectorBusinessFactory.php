<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\CalculationCheckoutConnector\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\CalculationCheckoutConnector\CalculationCheckoutConnectorDependencyProvider;
use Spryker\Zed\Calculation\Business\CalculationFacade;

class CalculationCheckoutConnectorBusinessFactory extends AbstractBusinessFactory
{

    /**
     * @return CalculationFacade
     */
    public function getCalculationFacade()
    {
        return $this->getProvidedDependency(CalculationCheckoutConnectorDependencyProvider::FACADE_CALCULATION);
    }

}