<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttributeGui\Business\Model;

use Orm\Zed\Product\Persistence\Map\SpyProductAttributeKeyTableMap;
use Orm\Zed\Product\Persistence\SpyProductAbstract;
use Orm\Zed\ProductManagement\Persistence\Map\SpyProductManagementAttributeTableMap;
use Orm\Zed\ProductManagement\Persistence\Map\SpyProductManagementAttributeValueTableMap;
use Orm\Zed\ProductManagement\Persistence\Map\SpyProductManagementAttributeValueTranslationTableMap;
use Orm\Zed\ProductManagement\Persistence\SpyProductManagementAttributeValueTranslationQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Criterion\AbstractCriterion;
use Propel\Runtime\Formatter\ArrayFormatter;
use Spryker\Zed\Product\Persistence\ProductQueryContainerInterface;
use Spryker\Zed\ProductManagement\Persistence\ProductManagementQueryContainerInterface;

class ProductAttributeManager
{

    /**
     * @var \Spryker\Zed\Product\Persistence\ProductQueryContainerInterface
     */
    protected $productQueryContainer;

    /**
     * @var \Spryker\Zed\ProductManagement\Persistence\ProductManagementQueryContainerInterface
     */
    protected $productManagementQueryContainer;

    /**
     * @param \Spryker\Zed\Product\Persistence\ProductQueryContainerInterface $productQueryContainer
     * @param \Spryker\Zed\ProductManagement\Persistence\ProductManagementQueryContainerInterface $productManagementQueryContainer
     */
    public function __construct(
        ProductQueryContainerInterface $productQueryContainer,
        ProductManagementQueryContainerInterface $productManagementQueryContainer
    ) {
        $this->productQueryContainer = $productQueryContainer;
        $this->productManagementQueryContainer = $productManagementQueryContainer;
    }

    /**
     * @param int $idProductAbstract
     *
     * @return array
     */
    public function getAttributes($idProductAbstract)
    {
        $values = $this->getProductAbstractAttributeValues($idProductAbstract);

        $valuesQuery = $this->queryProductAttributeValues($values);
        $valuesQuery->setPrimaryTableName(SpyProductManagementAttributeValueTableMap::TABLE_NAME);

        $values = $valuesQuery
            ->setFormatter(new ArrayFormatter())
            ->find()
            ->toArray();

        return $values;
    }

    /**
     * @param int $idProductAbstract
     *
     * @return array
     */
    protected function getProductAbstractAttributeValues($idProductAbstract)
    {
        $productAbstractEntity = $this->getProductAbstractEntity($idProductAbstract);

        $localizedAttributes = [];
        foreach ($productAbstractEntity->getSpyProductAbstractLocalizedAttributess() as $localizedAttributeEntity) {
            $attributesDecoded = $this->decodeJsonAttributes($localizedAttributeEntity->getAttributes());
            $localizedAttributes[$localizedAttributeEntity->getFkLocale()] = $attributesDecoded;
        }

        return $this->generateAttributes($productAbstractEntity, $localizedAttributes);
    }

    /**
     * @api
     *
     * @param array $productAttributes
     * @param bool $isSuper
     *
     * @return \Orm\Zed\ProductManagement\Persistence\SpyProductManagementAttributeValueTranslationQuery|\Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function queryProductAttributeValues(array $productAttributes = [], $isSuper = false)
    {
        $query = $this->productManagementQueryContainer
            ->queryProductManagementAttributeValueTranslation()
                ->joinSpyProductManagementAttributeValue()
                ->useSpyProductManagementAttributeValueQuery(null, Criteria::RIGHT_JOIN)
                    ->joinSpyProductManagementAttribute()
                    ->useSpyProductManagementAttributeQuery()
                        ->joinSpyProductAttributeKey()
                        ->useSpyProductAttributeKeyQuery()
                            ->filterByIsSuper($isSuper)
                        ->endUse()
                    ->endUse()
                ->endUse()
            ->withColumn(SpyProductAttributeKeyTableMap::COL_ID_PRODUCT_ATTRIBUTE_KEY, 'id_product_attribute_key')
            ->withColumn(SpyProductManagementAttributeTableMap::COL_ID_PRODUCT_MANAGEMENT_ATTRIBUTE, 'id_product_management_attribute')
            ->withColumn(SpyProductManagementAttributeValueTableMap::COL_ID_PRODUCT_MANAGEMENT_ATTRIBUTE_VALUE, 'id_product_management_attribute_value')
            ->withColumn(SpyProductManagementAttributeValueTranslationTableMap::COL_ID_PRODUCT_MANAGEMENT_ATTRIBUTE_VALUE_TRANSLATION, 'id_product_management_attribute_value_translation')
            ->withColumn(SpyProductAttributeKeyTableMap::COL_KEY, 'key')
            ->withColumn(SpyProductManagementAttributeValueTableMap::COL_VALUE, 'value')
            ->withColumn(SpyProductManagementAttributeValueTranslationTableMap::COL_FK_LOCALE, 'fk_locale')
            ->withColumn(SpyProductManagementAttributeValueTranslationTableMap::COL_TRANSLATION, 'translation')
            ->orderBy(SpyProductAttributeKeyTableMap::COL_KEY);

        $query = $this->createCriteria($query, $productAttributes);

        return $query;
    }

    /**
     * @param \Orm\Zed\ProductManagement\Persistence\SpyProductManagementAttributeValueTranslationQuery|\Propel\Runtime\ActiveQuery\ModelCriteria $query
     * @param array $productAttributes
     *
     * @return \Orm\Zed\ProductManagement\Persistence\SpyProductManagementAttributeValueTranslationQuery
     */
    protected function createCriteria(SpyProductManagementAttributeValueTranslationQuery $query, array $productAttributes)
    {
        $keys = $this->extractKeys($productAttributes);
        $criteria = new Criteria();

        $productAttributeKeyCriterion = $criteria->getNewCriterion(
            SpyProductAttributeKeyTableMap::COL_KEY,
            $keys,
            Criteria::IN
        );

        /** @var \Propel\Runtime\ActiveQuery\Criterion\AbstractCriterion $defaultCriterion */
        /** @var \Propel\Runtime\ActiveQuery\Criterion\AbstractCriterion $defaultLocalizedCriterion */
        $defaultCriterion = null;
        $defaultLocalizedCriterion = null;

        print_r($productAttributes);

        $criterionNullLocale = $criteria->getNewCriterion(
            SpyProductManagementAttributeValueTranslationTableMap::COL_FK_LOCALE,
            null,
            Criteria::ISNULL
        );

        foreach ($productAttributes as $idLocale => $localizedAttributes) {
            $criterionIdLocale = $criteria->getNewCriterion(
                SpyProductManagementAttributeValueTranslationTableMap::COL_FK_LOCALE,
                $idLocale
            );

            foreach ($localizedAttributes as $key => $value) {
                if ($idLocale === 'default') {
                    $criterionValue = $criteria->getNewCriterion(
                        SpyProductManagementAttributeValueTableMap::COL_VALUE,
                        '%' . mb_strtolower($value) . '%',
                        Criteria::LIKE
                    );

                    $defaultCriterion = $this->appendOrCriterion($criterionValue, $defaultCriterion);
                    //$defaultCriterion->addAnd($criterionNullLocale);
                } else {
                    $criterionTranslation = $criteria->getNewCriterion(
                        SpyProductManagementAttributeValueTranslationTableMap::COL_TRANSLATION,
                        '%' . mb_strtolower($value) . '%',
                        Criteria::LIKE
                    );

                    $criterionTranslation->addAnd($criterionIdLocale);
                    $defaultLocalizedCriterion = $this->appendOrCriterion($criterionTranslation, $defaultLocalizedCriterion);
                }
            }
        }

        $defaultCriterion->addOr($defaultLocalizedCriterion);
        $productAttributeKeyCriterion->addAnd($defaultCriterion);

        $criteria->addAnd($productAttributeKeyCriterion);
        $criteria->setIgnoreCase(true);

        $query->setIgnoreCase(true);
        $query->mergeWith($criteria, Criteria::LOGICAL_AND);

        return $query;
    }

    /**
     * @param string $localizedAttributesJson
     *
     * @return array
     */
    protected function decodeJsonAttributes($localizedAttributesJson)
    {
        $attributesDecoded = (array)json_decode($localizedAttributesJson, true);  //TODO util

        return $attributesDecoded;
    }

    /**
     * @param int $idProductAbstract
     *
     * @return array|mixed|\Orm\Zed\Product\Persistence\SpyProductAbstract
     */
    protected function getProductAbstractEntity($idProductAbstract)
    {
        return $this->productQueryContainer->queryProductAbstract()
            ->filterByIdProductAbstract($idProductAbstract)
            ->joinSpyProductAbstractLocalizedAttributes()
            ->findOne();
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstract $productAttributeEntity
     * @param array \Orm\Zed\Product\Persistence\SpyProductAbstractLocalizedAttributes[] $localizedAttributes
     *
     * @return array
     */
    protected function generateAttributes(SpyProductAbstract $productAttributeEntity, array $localizedAttributes)
    {
        $attributes = $this->decodeJsonAttributes($productAttributeEntity->getAttributes());
        $attributes = ['default' => $attributes] + $localizedAttributes;

        return $attributes;
    }

    /**
     * @param array $productAttributes
     *
     * @return array
     */
    protected function extractKeys(array $productAttributes)
    {
        $keys = [];
        foreach ($productAttributes as $idLocale => $localizedAttributes) {
            $keys = array_merge($keys, array_keys($localizedAttributes));
        }

        return array_unique($keys);
    }

    /**
     * @param array $productAttributes
     *
     * @return array
     */
    protected function extractValues($productAttributes)
    {
        $values = [];
        foreach ($productAttributes as $idLocale => $localizedAttributes) {
            $values = array_merge($values, array_values($localizedAttributes));
        }

        return array_unique($values);
    }

    /**
     * @param \Propel\Runtime\ActiveQuery\Criterion\AbstractCriterion $criterionToAppend
     * @param \Propel\Runtime\ActiveQuery\Criterion\AbstractCriterion|null $defaultCriterion
     *
     * @return \Propel\Runtime\ActiveQuery\Criterion\AbstractCriterion
     */
    protected function appendOrCriterion($criterionToAppend, AbstractCriterion $defaultCriterion = null)
    {
        if ($defaultCriterion === null) {
            $defaultCriterion = $criterionToAppend;
        } else {
            $defaultCriterion->addOr($criterionToAppend);
        }

        return $defaultCriterion;
    }

}