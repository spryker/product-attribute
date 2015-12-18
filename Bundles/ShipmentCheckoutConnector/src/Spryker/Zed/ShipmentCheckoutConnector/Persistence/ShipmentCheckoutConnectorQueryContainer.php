<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\ShipmentCheckoutConnector\Persistence;

use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;
use Orm\Zed\Sales\Persistence\SpySalesOrderQuery;
use Spryker\Zed\Sales\Persistence\SalesQueryContainerInterface;
use Orm\Zed\Shipment\Persistence\SpyShipmentMethodQuery;
use Spryker\Zed\Shipment\Persistence\ShipmentQueryContainerInterface;
use Spryker\Zed\ShipmentCheckoutConnector\ShipmentCheckoutConnectorDependencyProvider;

class ShipmentCheckoutConnectorQueryContainer extends AbstractQueryContainer implements ShipmentCheckoutConnectorQueryContainerInterface
{

    /**
     * @param $idSalesOrder
     *
     * @return SpySalesOrderQuery
     */
    public function querySalesOrderById($idSalesOrder)
    {
        return $this->getSalesQueryContainer()->querySalesOrderById($idSalesOrder);
    }

    /**
     * @param $idShipmentMethod
     *
     * @return SpyShipmentMethodQuery
     */
    public function queryShipmentOrderById($idShipmentMethod)
    {
        return $this->getShipmentQueryContainer()->queryMethodByIdMethod($idShipmentMethod);
    }

    /**
     * @return SalesQueryContainerInterface
     */
    protected function getSalesQueryContainer()
    {
        return $this->getProvidedDependency(ShipmentCheckoutConnectorDependencyProvider::QUERY_CONTAINER_SALES);
    }

    /**
     * @return ShipmentQueryContainerInterface
     */
    protected function getShipmentQueryContainer()
    {
        return $this->getProvidedDependency(ShipmentCheckoutConnectorDependencyProvider::QUERY_CONTAINER_SHIPMENT);
    }

}