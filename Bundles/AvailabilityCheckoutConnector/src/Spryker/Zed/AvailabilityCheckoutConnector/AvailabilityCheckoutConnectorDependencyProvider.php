<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\AvailabilityCheckoutConnector;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class AvailabilityCheckoutConnectorDependencyProvider extends AbstractBundleDependencyProvider
{

    const FACADE_AVAILABILITY = 'availability facade';

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container[self::FACADE_AVAILABILITY] = function (Container $container) {
            return $container->getLocator()->availability()->facade();
        };

        return $container;
    }

}