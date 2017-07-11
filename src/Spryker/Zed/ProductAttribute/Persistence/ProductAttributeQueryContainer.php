<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttribute\Persistence;

use Orm\Zed\Product\Persistence\Map\SpyProductAttributeKeyTableMap;
use Orm\Zed\Product\Persistence\SpyProductAttributeKeyQuery;
use Orm\Zed\ProductAttribute\Persistence\Map\SpyProductManagementAttributeValueTableMap;
use Orm\Zed\ProductAttribute\Persistence\Map\SpyProductManagementAttributeValueTranslationTableMap;
use PDO;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Criterion\AbstractCriterion;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * @method \Spryker\Zed\ProductAttribute\Persistence\ProductAttributePersistenceFactory getFactory()
 */
class ProductAttributeQueryContainer extends AbstractQueryContainer implements ProductAttributeQueryContainerInterface
{

    /**
     * @api
     *
     * @return \Orm\Zed\ProductAttribute\Persistence\SpyProductManagementAttributeQuery
     */
    public function queryProductManagementAttribute()
    {
        return $this->getFactory()->createProductManagementAttributeQuery();
    }

    /**
     * @api
     *
     * @return \Orm\Zed\ProductAttribute\Persistence\SpyProductManagementAttributeValueQuery
     */
    public function queryProductManagementAttributeValue()
    {
        return $this->getFactory()->createProductManagementAttributeValueQuery();
    }

    /**
     * @api
     *
     * @return \Orm\Zed\Product\Persistence\SpyProductAttributeKeyQuery
     */
    public function queryProductAttributeKey()
    {
        return $this->getFactory()->createProductAttributeKeyQuery();
    }

    /**
     * @api
     *
     * @return \Orm\Zed\ProductAttribute\Persistence\SpyProductManagementAttributeValueQuery
     */
    public function queryProductManagementAttributeValueQuery()
    {
        return $this->getFactory()
            ->createProductManagementAttributeValueQuery();
    }

    /**
     * @api
     *
     * @return \Orm\Zed\ProductAttribute\Persistence\SpyProductManagementAttributeValueTranslationQuery
     */
    public function queryProductManagementAttributeValueTranslation()
    {
        return $this->getFactory()
            ->createProductManagementAttributeValueTranslationQuery();
    }

    /**
     * @api
     *
     * @param array $attributeKeys
     *
     * @return \Orm\Zed\Product\Persistence\SpyProductAttributeKeyQuery
     */
    public function queryMetaAttributesByKeys(array $attributeKeys)
    {
        return $this
            ->queryProductAttributeKey()
            ->leftJoinSpyProductManagementAttribute()
            ->filterByKey_In($attributeKeys)
            ->setIgnoreCase(true);
    }

    /**
     * @api
     *
     * @param string $searchText
     * @param int $limit
     *
     * @return \Orm\Zed\Product\Persistence\SpyProductAttributeKeyQuery
     */
    public function querySuggestKeys($searchText, $limit = 10)
    {
        $query = $this
            ->queryProductAttributeKey()
            ->filterByIsSuper(false)
            ->useSpyProductManagementAttributeQuery()
            ->endUse()
            ->limit($limit);

        $searchText = trim($searchText);
        if ($searchText !== '') {
            $term = '%' . mb_strtoupper($searchText) . '%';

            $query->where('UPPER(' . SpyProductAttributeKeyTableMap::COL_KEY . ') LIKE ?', $term, PDO::PARAM_STR);
        }

        return $query;
    }

    /**
     * @api
     *
     * @param int $idProductManagementAttribute
     * @param int $idLocale
     *
     * @return \Orm\Zed\ProductAttribute\Persistence\SpyProductManagementAttributeValueQuery
     */
    public function queryProductManagementAttributeValueWithTranslation($idProductManagementAttribute, $idLocale)
    {
        return $this->getFactory()
            ->createProductManagementAttributeValueQuery()
            ->clearSelectColumns()
            ->filterByFkProductManagementAttribute($idProductManagementAttribute)
            ->addJoin(
                [
                    SpyProductManagementAttributeValueTableMap::COL_ID_PRODUCT_MANAGEMENT_ATTRIBUTE_VALUE,
                    (int)$idLocale,
                ],
                [
                    SpyProductManagementAttributeValueTranslationTableMap::COL_FK_PRODUCT_MANAGEMENT_ATTRIBUTE_VALUE,
                    SpyProductManagementAttributeValueTranslationTableMap::COL_FK_LOCALE,
                ],
                Criteria::LEFT_JOIN
            )
            ->withColumn(SpyProductManagementAttributeValueTableMap::COL_ID_PRODUCT_MANAGEMENT_ATTRIBUTE_VALUE, 'id_product_management_attribute_value')
            ->withColumn(SpyProductManagementAttributeValueTableMap::COL_VALUE, 'value')
            ->withColumn($idLocale, 'fk_locale')
            ->withColumn(SpyProductManagementAttributeValueTranslationTableMap::COL_TRANSLATION, 'translation');
    }

    /**
     * @api
     *
     * @return \Orm\Zed\Product\Persistence\SpyProductAttributeKeyQuery
     */
    public function queryUnusedProductAttributeKeys()
    {
        return $this
            ->queryProductAttributeKey()
            ->addSelectColumn(SpyProductAttributeKeyTableMap::COL_KEY)
            ->useSpyProductManagementAttributeQuery(null, Criteria::LEFT_JOIN)
            ->filterByIdProductManagementAttribute(null)
            ->endUse();
    }

    /**
     * @api
     *
     * @param int $idProductManagementAttribute
     *
     * @return \Orm\Zed\ProductAttribute\Persistence\SpyProductManagementAttributeValueTranslationQuery
     */
    public function queryProductManagementAttributeValueTranslationById($idProductManagementAttribute)
    {
        return $this
            ->queryProductManagementAttributeValueTranslation()
            ->joinSpyProductManagementAttributeValue()
            ->useSpyProductManagementAttributeValueQuery()
            ->filterByFkProductManagementAttribute($idProductManagementAttribute)
            ->endUse();
    }

    /**
     * @api
     *
     * @param array $attributes
     * @param bool|null $isSuper
     *
     * @return \Orm\Zed\Product\Persistence\SpyProductAttributeKeyQuery
     */
    public function queryAttributeValues(array $attributes = [], $isSuper = null)
    {
        $query = $this->queryProductAttributeKey()
            ->useSpyProductManagementAttributeQuery(null, Criteria::LEFT_JOIN)
                ->useSpyProductManagementAttributeValueQuery(null, Criteria::LEFT_JOIN)
                    ->useSpyProductManagementAttributeValueTranslationQuery(null, Criteria::LEFT_JOIN)
                    ->endUse()
                ->endUse()
            ->endUse();

        $query = $this->appendAttributeValuesCriteria($query, $attributes);

        if ($isSuper !== null) {
            $query->filterByIsSuper($isSuper);
        }

        return $query;
    }

    /**
     * $attributes format
     * [
     *   [_] => [key=>value, key2=>value2]
     *   [46] => [key=>value]
     *   [66] => [key3=>value3, key5=value5]
     * ]
     *
     * @see ProductAttributeConfig::DEFAULT_LOCALE
     *
     * @param \Orm\Zed\Product\Persistence\SpyProductAttributeKeyQuery $query
     * @param array $attributes
     *
     * @return \Orm\Zed\Product\Persistence\SpyProductAttributeKeyQuery
     */
    protected function appendAttributeValuesCriteria(SpyProductAttributeKeyQuery $query, array $attributes)
    {
        /** @var \Propel\Runtime\ActiveQuery\Criterion\AbstractCriterion $defaultCriterion */
        /** @var \Propel\Runtime\ActiveQuery\Criterion\AbstractCriterion $defaultLocalizedCriterion */
        $defaultCriterion = null;
        $defaultLocalizedCriterion = null;
        $criteria = new Criteria();

        foreach ($attributes as $idLocale => $localizedAttributes) {
            foreach ($localizedAttributes as $key => $value) {
                $criterionValue = $criteria->getNewCriterion(
                    SpyProductManagementAttributeValueTableMap::COL_VALUE,
                    '%' . mb_strtolower($value) . '%',
                    Criteria::LIKE
                );

                $criterionTranslation = $criteria->getNewCriterion(
                    SpyProductManagementAttributeValueTranslationTableMap::COL_TRANSLATION,
                    '%' . mb_strtolower($value) . '%',
                    Criteria::LIKE
                );

                $criterionValue->addOr($criterionTranslation);
                $defaultCriterion = $this->appendOrCriterion($criterionValue, $defaultCriterion);
            }
        }

        $productAttributeKeyCriterion = $this->createAttributeKeysInCriterion(
            $attributes,
            $criteria,
            $defaultCriterion
        );

        $criteria->addAnd($productAttributeKeyCriterion);
        $criteria->setIgnoreCase(true);

        $query->setIgnoreCase(true);
        $query->mergeWith($criteria, Criteria::LOGICAL_AND);

        return $query;
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

    /**
     * @param array $keys
     * @param \Propel\Runtime\ActiveQuery\Criteria $criteria
     * @param \Propel\Runtime\ActiveQuery\Criterion\AbstractCriterion $defaultCriterion
     *
     * @return mixed
     */
    protected function createAttributeKeysInCriterion(array $keys, Criteria $criteria, AbstractCriterion $defaultCriterion)
    {
        $productAttributeKeyCriterion = $criteria->getNewCriterion(
            SpyProductAttributeKeyTableMap::COL_KEY,
            $keys,
            Criteria::IN
        );
        $productAttributeKeyCriterion->addAnd($defaultCriterion);

        return $productAttributeKeyCriterion;
    }

}