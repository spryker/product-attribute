<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttribute\Business\Model\Product;

use Generated\Shared\Transfer\ProductAttributeQueryCriteriaTransfer;
use Orm\Zed\Product\Persistence\Map\SpyProductAttributeKeyTableMap;
use Orm\Zed\ProductAttribute\Persistence\Map\SpyProductManagementAttributeTableMap;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Formatter\ArrayFormatter;
use Spryker\Zed\ProductAttribute\Business\Model\Product\Mapper\ProductAttributeMapperInterface;
use Spryker\Zed\ProductAttribute\Dependency\Facade\ProductAttributeToProductInterface;
use Spryker\Zed\ProductAttribute\Persistence\ProductAttributeQueryContainer;
use Spryker\Zed\ProductAttribute\Persistence\ProductAttributeQueryContainerInterface;

class ProductAttributeReader implements ProductAttributeReaderInterface
{
    /**
     * @var \Spryker\Zed\ProductAttribute\Persistence\ProductAttributeQueryContainerInterface
     */
    protected $productAttributeQueryContainer;

    /**
     * @var \Spryker\Zed\ProductAttribute\Business\Model\Product\Mapper\ProductAttributeMapperInterface
     */
    protected $attributeMapper;

    /**
     * @var \Spryker\Zed\ProductAttribute\Dependency\Facade\ProductAttributeToProductInterface
     */
    protected $productFacade;

    /**
     * @var array<\Spryker\Zed\ProductAttributeExtension\Dependency\Plugin\ProductAttributeQueryExpanderPluginInterface>
     */
    protected array $productAttributeQueryExpanderPlugins;

    /**
     * @var array<\Spryker\Zed\ProductAttributeExtension\Dependency\Plugin\SuggestKeysQueryExpanderPluginInterface>
     */
    protected array $suggestKeysQueryExpanderPlugins;

    /**
     * @var array<\Spryker\Zed\ProductAttributeExtension\Dependency\Plugin\SuggestKeysExpanderPluginInterface>
     */
    protected array $suggestKeysExpanderPlugins;

    /**
     * @param array<\Spryker\Zed\ProductAttributeExtension\Dependency\Plugin\ProductAttributeQueryExpanderPluginInterface> $productAttributeQueryExpanderPlugins
     * @param array<\Spryker\Zed\ProductAttributeExtension\Dependency\Plugin\SuggestKeysQueryExpanderPluginInterface> $suggestKeysQueryExpanderPlugins
     * @param array<\Spryker\Zed\ProductAttributeExtension\Dependency\Plugin\SuggestKeysExpanderPluginInterface> $suggestKeysExpanderPlugins
     */
    public function __construct(
        ProductAttributeQueryContainerInterface $productAttributeQueryContainer,
        ProductAttributeMapperInterface $attributeMapper,
        ProductAttributeToProductInterface $productFacade,
        array $productAttributeQueryExpanderPlugins = [],
        array $suggestKeysQueryExpanderPlugins = [],
        array $suggestKeysExpanderPlugins = [],
    ) {
        $this->productAttributeQueryContainer = $productAttributeQueryContainer;
        $this->attributeMapper = $attributeMapper;
        $this->productFacade = $productFacade;
        $this->productAttributeQueryExpanderPlugins = $productAttributeQueryExpanderPlugins;
        $this->suggestKeysQueryExpanderPlugins = $suggestKeysQueryExpanderPlugins;
        $this->suggestKeysExpanderPlugins = $suggestKeysExpanderPlugins;
    }

    /**
     * @param array $values
     *
     * @return array
     */
    public function getMetaAttributesByValues(array $values)
    {
        $query = $this->queryMetaAttributes($values);
        $query->setFormatter(new ArrayFormatter());

        return $this->attributeMapper->mapMetaAttributes($query->find());
    }

    /**
     * @param string $searchText
     * @param int $limit
     *
     * @return array
     */
    public function suggestKeys($searchText = '', $limit = 10)
    {
        $query = $this->querySuggestKeys($searchText, $limit);
        $suggestKeys = $this->attributeMapper->metaAttributeSuggestKeys($query->find());

        foreach ($this->suggestKeysExpanderPlugins as $suggestKeysExpanderPlugin) {
            $suggestKeys = $suggestKeysExpanderPlugin->expandSuggestKeys($suggestKeys);
        }

        return $suggestKeys;
    }

    /**
     * @param array $productAttributes
     *
     * @return \Propel\Runtime\ActiveQuery\Criteria|\Orm\Zed\Product\Persistence\SpyProductAttributeKeyQuery
     */
    protected function queryMetaAttributes(array $productAttributes)
    {
        $keys = $this->attributeMapper->extractKeysFromAttributes($productAttributes);

        $query = $this->productAttributeQueryContainer
            ->queryMetaAttributesByKeys($keys)
            ->clearSelectColumns()
            ->withColumn(SpyProductAttributeKeyTableMap::COL_KEY, ProductAttributeQueryContainer::KEY)
            ->withColumn(SpyProductAttributeKeyTableMap::COL_IS_SUPER, ProductAttributeQueryContainer::IS_SUPER)
            ->withColumn(SpyProductManagementAttributeTableMap::COL_ID_PRODUCT_MANAGEMENT_ATTRIBUTE, ProductAttributeQueryContainer::ATTRIBUTE_ID)
            ->withColumn(SpyProductManagementAttributeTableMap::COL_ALLOW_INPUT, ProductAttributeQueryContainer::ALLOW_INPUT)
            ->withColumn(SpyProductManagementAttributeTableMap::COL_INPUT_TYPE, ProductAttributeQueryContainer::INPUT_TYPE);

        $query = $this->executeProductAttributeQueryExpanderPlugins($query);

        return $query;
    }

    /**
     * @param string $searchText
     * @param int $limit
     *
     * @return \Orm\Zed\Product\Persistence\SpyProductAttributeKeyQuery|\Propel\Runtime\ActiveQuery\ModelCriteria
     */
    protected function querySuggestKeys($searchText, $limit = 10)
    {
        $query = $this->productAttributeQueryContainer
            ->querySuggestKeys($searchText, $limit)
            ->clearSelectColumns()
            ->withColumn(SpyProductAttributeKeyTableMap::COL_KEY, ProductAttributeQueryContainer::KEY)
            ->withColumn(SpyProductAttributeKeyTableMap::COL_IS_SUPER, ProductAttributeQueryContainer::IS_SUPER)
            ->withColumn(SpyProductManagementAttributeTableMap::COL_ID_PRODUCT_MANAGEMENT_ATTRIBUTE, ProductAttributeQueryContainer::ATTRIBUTE_ID)
            ->withColumn(SpyProductManagementAttributeTableMap::COL_ALLOW_INPUT, ProductAttributeQueryContainer::ALLOW_INPUT)
            ->withColumn(SpyProductManagementAttributeTableMap::COL_INPUT_TYPE, ProductAttributeQueryContainer::INPUT_TYPE)
            ->orderByKey()
            ->setFormatter(new ArrayFormatter());

        $query = $this->executeSuggestKeysQueryExpanderPlugins($query);

        return $query;
    }

    protected function executeProductAttributeQueryExpanderPlugins(ModelCriteria $query): ModelCriteria
    {
        $productAttributeQueryCriteriaTransfer = new ProductAttributeQueryCriteriaTransfer();

        foreach ($this->productAttributeQueryExpanderPlugins as $plugin) {
            $productAttributeQueryCriteriaTransfer = $plugin->expandProductAttributeQueryCriteria($productAttributeQueryCriteriaTransfer);
        }

        return $this->applyQueryCriteria($query, $productAttributeQueryCriteriaTransfer);
    }

    protected function executeSuggestKeysQueryExpanderPlugins(ModelCriteria $query): ModelCriteria
    {
        $productAttributeQueryCriteriaTransfer = new ProductAttributeQueryCriteriaTransfer();

        foreach ($this->suggestKeysQueryExpanderPlugins as $plugin) {
            $productAttributeQueryCriteriaTransfer = $plugin->expandSuggestKeysQueryCriteria($productAttributeQueryCriteriaTransfer);
        }

        return $this->applyQueryCriteria($query, $productAttributeQueryCriteriaTransfer);
    }

    protected function applyQueryCriteria(ModelCriteria $query, ProductAttributeQueryCriteriaTransfer $productAttributeQueryCriteriaTransfer): ModelCriteria
    {
        foreach ($productAttributeQueryCriteriaTransfer->getWithColumns() as $column => $alias) {
            $query->withColumn($column, $alias);
        }

        return $query;
    }
}
