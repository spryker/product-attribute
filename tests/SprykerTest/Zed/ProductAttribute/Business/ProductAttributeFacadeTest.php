<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductAttribute\Business;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\ProductManagementAttributeTransfer;
use Generated\Shared\Transfer\ProductManagementAttributeValueTransfer;
use Generated\Shared\Transfer\ProductManagementAttributeValueTranslationTransfer;
use Orm\Zed\ProductAttribute\Persistence\SpyProductManagementAttributeValueTranslation;
use Spryker\Shared\ProductAttribute\Code\KeyBuilder\AttributeGlossaryKeyBuilder;
use Spryker\Zed\Product\Business\ProductFacade;
use Spryker\Zed\ProductAttribute\Business\Model\Attribute\AttributeTranslator;
use Spryker\Zed\ProductAttribute\Business\ProductAttributeBusinessFactory;
use Spryker\Zed\ProductAttribute\Business\ProductAttributeFacade;
use Spryker\Zed\ProductAttribute\Dependency\Facade\ProductAttributeToGlossaryBridge;
use Spryker\Zed\ProductAttribute\Dependency\Facade\ProductAttributeToLocaleBridge;
use Spryker\Zed\ProductAttribute\Dependency\Facade\ProductAttributeToProductBridge;
use Spryker\Zed\ProductAttribute\Persistence\ProductAttributeQueryContainer;
use SprykerTest\Zed\ProductAttribute\ProductAttributeBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductAttribute
 * @group Business
 * @group Facade
 * @group ProductAttributeFacadeTest
 * Add your own group annotations below this line
 */
class ProductAttributeFacadeTest extends Unit
{
    /**
     * @var \Spryker\Zed\Product\Business\ProductFacade
     */
    protected $productFacade;

    /**
     * @var \Spryker\Zed\ProductAttribute\Business\ProductAttributeFacade
     */
    protected $productAttributeFacade;

    /**
     * @var \Spryker\Zed\ProductAttribute\Persistence\ProductAttributeQueryContainer
     */
    protected $productAttributeQueryContainer;

    /**
     * @var \SprykerTest\Zed\ProductAttribute\ProductAttributeBusinessTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->productAttributeFacade = new ProductAttributeFacade();
        $this->productAttributeQueryContainer = new ProductAttributeQueryContainer();

        $this->productFacade = new ProductAttributeToProductBridge(new ProductFacade());

        $this->tester->setProductFacade($this->productFacade);
        $this->tester->setProductAttributeFacade($this->productAttributeFacade);
    }

    /**
     * @return void
     */
    public function testTranslateProductManagementAttributeKeyInGlossary(): void
    {
        /** @var \Spryker\Zed\ProductAttribute\Business\ProductAttributeBusinessFactory|\PHPUnit\Framework\MockObject\MockObject $productManagementBusinessFactoryMock */
        $productManagementBusinessFactoryMock = $this->getMockBuilder(ProductAttributeBusinessFactory::class)
            ->setMethods(['createAttributeTranslator'])
            ->getMock();

        $productManagementBusinessFactoryMock->method('createAttributeTranslator')
            ->willReturn($this->getAttributeTranslatorMock());

        $this->productAttributeFacade->setFactory($productManagementBusinessFactoryMock);

        $productAttributeKeyEntity = $this->tester->createProductManagementAttributeEntity();

        $productManagementAttributeTransfer = $this->tester->generateProductManagementAttributeTransfer()
            ->setIdProductManagementAttribute($productAttributeKeyEntity->getIdProductManagementAttribute())
            ->setLocalizedKeys(new ArrayObject([
                $this->tester->generateLocalizedProductManagementAttributeKeyTransfer([
                    'locale_name' => 'aa_AA',
                    'key_translation' => 'Foo',
                ]),
                $this->tester->generateLocalizedProductManagementAttributeKeyTransfer([
                    'locale_name' => 'bb_BB',
                    'key_translation' => 'Bar',
                ]),
            ]));

        $this->productAttributeFacade->translateProductManagementAttribute($productManagementAttributeTransfer);
    }

    /**
     * @return void
     */
    public function testUpdateProductManagementAttributeReturnsValuesWithIds(): void
    {
        // Arrange
        $productManagementAttributeEntity = $this->tester->createProductManagementAttributeEntity(['a', 'b', 'c']);
        $productManagementAttributeTransfer = (new ProductManagementAttributeTransfer())
            ->setIdProductManagementAttribute($productManagementAttributeEntity->getIdProductManagementAttribute())
            ->setInputType($productManagementAttributeEntity->getInputType())
            ->setKey($productManagementAttributeEntity->getSpyProductAttributeKey()->getKey())
            ->addValue((new ProductManagementAttributeValueTransfer())->setValue('a'))
            ->addValue((new ProductManagementAttributeValueTransfer())->setValue('b'))
            ->addValue((new ProductManagementAttributeValueTransfer())->setValue('d'));
        // Act
        $productManagementAttributeTransfer = $this->productAttributeFacade->updateProductManagementAttribute($productManagementAttributeTransfer);

        // Assert
        $this->assertCount(3, $productManagementAttributeTransfer->getValues());
        $this->assertNotNull($productManagementAttributeTransfer->getValues()[0]->getIdProductManagementAttributeValue());
        $this->assertNotNull($productManagementAttributeTransfer->getValues()[1]->getIdProductManagementAttributeValue());
        $this->assertNotNull($productManagementAttributeTransfer->getValues()[2]->getIdProductManagementAttributeValue());
        $this->assertSame('a', $productManagementAttributeTransfer->getValues()[0]->getValue());
        $this->assertSame('b', $productManagementAttributeTransfer->getValues()[1]->getValue());
        $this->assertSame('d', $productManagementAttributeTransfer->getValues()[2]->getValue());
    }

    /**
     * @return void
     */
    public function testTranslateProductManagementAttributeValues(): void
    {
        /** @var \Spryker\Zed\ProductAttribute\Business\ProductAttributeBusinessFactory|\PHPUnit\Framework\MockObject\MockObject $productManagementBusinessFactoryMock */
        $productManagementBusinessFactoryMock = $this->getMockBuilder(ProductAttributeBusinessFactory::class)
            ->setMethods(['createAttributeTranslator'])
            ->getMock();

        $productManagementBusinessFactoryMock->method('createAttributeTranslator')
            ->willReturn($this->getAttributeTranslatorMock());

        $this->productAttributeFacade->setFactory($productManagementBusinessFactoryMock);

        $productAttributeKeyEntity = $this->tester->createProductManagementAttributeEntity(['a', 'b', 'c']);

        $productManagementAttributeTransfer = (new ProductManagementAttributeTransfer())
            ->setIdProductManagementAttribute($productAttributeKeyEntity->getIdProductManagementAttribute());

        foreach ($productAttributeKeyEntity->getSpyProductManagementAttributeValues() as $productManagementAttributeValueEntity) {
            $productManagementAttributeValueTransfer = (new ProductManagementAttributeValueTransfer())
                ->fromArray($productManagementAttributeValueEntity->toArray(), true);

            $productManagementAttributeValueTransfer
                ->addLocalizedValue(
                    (new ProductManagementAttributeValueTranslationTransfer())
                        ->setFkLocale($this->tester->getLocale('aa_AA')->getIdLocale())
                        ->setTranslation($productManagementAttributeValueEntity->getValue() . ' translated to a language'),
                )
                ->addLocalizedValue(
                    (new ProductManagementAttributeValueTranslationTransfer())
                        ->setFkLocale($this->tester->getLocale('bb_BB')->getIdLocale())
                        ->setTranslation($productManagementAttributeValueEntity->getValue() . ' translated to another language'),
                );

            $productManagementAttributeTransfer->addValue($productManagementAttributeValueTransfer);
        }

        $this->productAttributeFacade->translateProductManagementAttribute($productManagementAttributeTransfer);
    }

    /**
     * @return void
     */
    public function testGetProductManagementAttributeReturnsNullIfEntityDoesNotExist(): void
    {
        $this->assertNull($this->productAttributeFacade->getProductManagementAttribute(0));
    }

    /**
     * @return void
     */
    public function testGetProductManagementAttributeReturnsFullyHydratedTransfer(): void
    {
        $productAttributeKeyEntity = $this->tester->createProductManagementAttributeEntity(['a', 'b', 'c']);

        $productManagementAttributeTransfer = (new ProductManagementAttributeTransfer())
            ->setIdProductManagementAttribute($productAttributeKeyEntity->getIdProductManagementAttribute());

        foreach ($productAttributeKeyEntity->getSpyProductManagementAttributeValues() as $productManagementAttributeValueEntity) {
            $attributeValueTranslationEntity = new SpyProductManagementAttributeValueTranslation();
            $attributeValueTranslationEntity
                ->setFkProductManagementAttributeValue($productManagementAttributeValueEntity->getIdProductManagementAttributeValue())
                ->setFkLocale($this->tester->getLocale('aa_AA')->getIdLocale())
                ->setTranslation($productManagementAttributeValueEntity->getValue() . ' translated to a language')
                ->save();

            $attributeValueTranslationEntity = new SpyProductManagementAttributeValueTranslation();
            $attributeValueTranslationEntity
                ->setFkProductManagementAttributeValue($productManagementAttributeValueEntity->getIdProductManagementAttributeValue())
                ->setFkLocale($this->tester->getLocale('bb_BB')->getIdLocale())
                ->setTranslation($productManagementAttributeValueEntity->getValue() . ' translated to another language')
                ->save();
        }

        $productManagementAttributeTransfer = $this->productAttributeFacade->getProductManagementAttribute(
            $productManagementAttributeTransfer->getIdProductManagementAttribute(),
        );

        $this->assertNotNull($productManagementAttributeTransfer);
        $this->assertCount(3, $productManagementAttributeTransfer->getValues());
        $this->assertCount(2, $productManagementAttributeTransfer->getValues()[0]->getLocalizedValues());
    }

    /**
     * @return void
     */
    public function testGetProductAbstractAttributeValues(): void
    {
        $productAbstractTransfer = $this->tester->createSampleAbstractProduct(ProductAttributeBusinessTester::ABSTRACT_SKU);

        $productAttributesValues = $this->productAttributeFacade->getProductAbstractAttributeValues(
            $productAbstractTransfer->getIdProductAbstract(),
        );

        $this->assertSame($this->tester->getSampleLocalizedProductAttributeValues(), $productAttributesValues);
    }

    /**
     * @return void
     */
    public function testGetProductAttributeValues(): void
    {
        $productAbstractTransfer = $this->tester->createSampleAbstractProduct(ProductAttributeBusinessTester::ABSTRACT_SKU);
        $productTransfer = $this->tester->createSampleProduct($productAbstractTransfer, ProductAttributeBusinessTester::CONCRETE_SKU);

        $productValues = $this->productAttributeFacade->getProductAttributeValues(
            $productTransfer->getIdProductConcrete(),
        );

        $this->assertSame($this->tester->getSampleLocalizedProductAttributeValues(), $productValues);
    }

    /**
     * @return void
     */
    public function testGetMetaAttributesForProductAbstractShouldReturnEmptySetForUndefinedAttributes(): void
    {
        $productAbstractTransfer = $this->tester->createSampleAbstractProduct(ProductAttributeBusinessTester::ABSTRACT_SKU);

        $metaAttributes = $this->productAttributeFacade->getMetaAttributesForProductAbstract(
            $productAbstractTransfer->getIdProductAbstract(),
        );

        $this->assertEmpty($metaAttributes);
    }

    /**
     * @return void
     */
    public function testGetMetaAttributesForProductShouldReturnEmptySetForUndefinedAttributes(): void
    {
        $productAbstractTransfer = $this->tester->createSampleAbstractProduct(ProductAttributeBusinessTester::ABSTRACT_SKU);
        $productTransfer = $this->tester->createSampleProduct($productAbstractTransfer, ProductAttributeBusinessTester::CONCRETE_SKU);

        $metaAttributes = $this->productAttributeFacade->getMetaAttributesForProduct(
            $productTransfer->getIdProductConcrete(),
        );

        $this->assertEmpty($metaAttributes);
    }

    /**
     * @return void
     */
    public function testGetMetaAttributesForProductAbstract(): void
    {
        $data = $this->tester->createSampleAttributeMetadataWithSuperAttributeData();
        $productAbstractTransfer = $this->tester->createSampleAbstractProduct(ProductAttributeBusinessTester::ABSTRACT_SKU, $data);

        $metaAttributes = $this->productAttributeFacade->getMetaAttributesForProductAbstract(
            $productAbstractTransfer->getIdProductAbstract(),
        );

        $this->assertNotEmpty($metaAttributes);
        $this->assertArrayHasKey(ProductAttributeBusinessTester::FOO_ATTRIBUTE_KEY, $metaAttributes);
        $this->assertArrayHasKey(ProductAttributeBusinessTester::SUPER_ATTRIBUTE_KEY, $metaAttributes);
    }

    /**
     * @return void
     */
    public function testGetMetaAttributesForProduct(): void
    {
        $data = $this->tester->createSampleAttributeMetadataWithSuperAttributeData();
        $productAbstractTransfer = $this->tester->createSampleAbstractProduct(ProductAttributeBusinessTester::ABSTRACT_SKU);
        $productTransfer = $this->tester->createSampleProduct($productAbstractTransfer, ProductAttributeBusinessTester::CONCRETE_SKU, $data);

        $metaAttributes = $this->productAttributeFacade->getMetaAttributesForProduct(
            $productTransfer->getIdProductConcrete(),
        );

        $this->assertNotEmpty($metaAttributes);
        $this->assertArrayHasKey(ProductAttributeBusinessTester::FOO_ATTRIBUTE_KEY, $metaAttributes);
        $this->assertArrayHasKey(ProductAttributeBusinessTester::SUPER_ATTRIBUTE_KEY, $metaAttributes);
    }

    /**
     * @return void
     */
    public function testSaveAbstractAttributes(): void
    {
        $productAbstractTransfer = $this->tester->createSampleAbstractProduct(ProductAttributeBusinessTester::ABSTRACT_SKU);
        $fooMetaAttributeTransfer = $this->tester->createSampleAttributeMetadata(ProductAttributeBusinessTester::FOO_ATTRIBUTE_KEY, false);

        $attributesToSave = [
            [
                'key' => $fooMetaAttributeTransfer->getKey(),
                'id' => $fooMetaAttributeTransfer->getIdProductManagementAttribute(),
                'locale_code' => '_',
                'value' => 'New Foo Value',

            ],
            [
                'key' => 'undefined__key',
                'id' => null,
                'locale_code' => $this->tester->getLocaleOne()->getLocaleName(),
                'value' => 'xxx',
            ],
        ];

        $this->productAttributeFacade->saveAbstractAttributes(
            $productAbstractTransfer->getIdProductAbstract(),
            $attributesToSave,
        );

        $productAttributesValues = $this->productAttributeFacade->getProductAbstractAttributeValues(
            $productAbstractTransfer->getIdProductAbstract(),
        );

        $expectedResult = [
            '_' => [
                'foo' => 'New Foo Value',
            ],
            $this->tester->getLocaleOne()->getLocaleName() => [
                'undefined__key' => 'xxx',
            ],
            $this->tester->getLocaleTwo()->getLocaleName() => [],
        ];

        ksort($expectedResult);

        $this->assertSame($expectedResult, $productAttributesValues);
    }

    /**
     * @return void
     */
    public function testSaveConcreteAttributes(): void
    {
        $productAbstractTransfer = $this->tester->createSampleAbstractProduct(ProductAttributeBusinessTester::ABSTRACT_SKU);
        $productTransfer = $this->tester->createSampleProduct($productAbstractTransfer, ProductAttributeBusinessTester::CONCRETE_SKU);
        $fooMetaAttributeTransfer = $this->tester->createSampleAttributeMetadata(ProductAttributeBusinessTester::FOO_ATTRIBUTE_KEY, false);

        $attributesToSave = [
            [
                'key' => $fooMetaAttributeTransfer->getKey(),
                'id' => $fooMetaAttributeTransfer->getIdProductManagementAttribute(),
                'locale_code' => '_',
                'value' => 'New Foo Value',
            ],
            [
                'key' => $fooMetaAttributeTransfer->getKey(),
                'id' => $fooMetaAttributeTransfer->getIdProductManagementAttribute(),
                'locale_code' => $this->tester->getLocaleOne()->getLocaleName(),
                'value' => '',
            ],
            [
                'key' => $fooMetaAttributeTransfer->getKey(),
                'id' => $fooMetaAttributeTransfer->getIdProductManagementAttribute(),
                'locale_code' => $this->tester->getLocaleTwo()->getLocaleName(),
                'value' => '',
            ],
            [
                'key' => 'undefined__key',
                'id' => null,
                'locale_code' => $this->tester->getLocaleOne()->getLocaleName(),
                'value' => 'xxx',
            ],
        ];

        $this->productAttributeFacade->saveConcreteAttributes(
            $productTransfer->getIdProductConcrete(),
            $attributesToSave,
        );

        $productAttributesValues = $this->productAttributeFacade->getProductAttributeValues(
            $productTransfer->getIdProductConcrete(),
        );

        $expectedResult = [
            '_' => [
                'foo' => 'New Foo Value',
            ],
            $this->tester->getLocaleOne()->getLocaleName() => [
                'undefined__key' => 'xxx',
            ],
            $this->tester->getLocaleTwo()->getLocaleName() => [],
        ];

        ksort($expectedResult);

        $this->assertSame($expectedResult, $productAttributesValues);
    }

    /**
     * @return void
     */
    public function testSuggestKeys(): void
    {
        $this->tester->createSampleAttributeMetadata(ProductAttributeBusinessTester::FOO_ATTRIBUTE_KEY, false);
        $this->tester->createSampleAttributeMetadata(ProductAttributeBusinessTester::SUPER_ATTRIBUTE_KEY, true);

        $suggestedKeys = $this->productAttributeFacade->suggestKeys(ProductAttributeBusinessTester::FOO_ATTRIBUTE_KEY);

        $this->assertNotEmpty($suggestedKeys);
    }

    /**
     * @return void
     */
    public function testSuggestKeysShouldIgnoreSuperAttributes(): void
    {
        $this->tester->createSampleAttributeMetadata(ProductAttributeBusinessTester::FOO_ATTRIBUTE_KEY, false);
        $this->tester->createSampleAttributeMetadata(ProductAttributeBusinessTester::SUPER_ATTRIBUTE_KEY, true);

        $suggestedKeys = $this->productAttributeFacade->suggestKeys(ProductAttributeBusinessTester::SUPER_ATTRIBUTE_KEY);

        $this->assertEmpty($suggestedKeys);
    }

    /**
     * @return void
     */
    public function testExtractKeysFromAttributes(): void
    {
        $keys = $this->productAttributeFacade->extractKeysFromAttributes($this->tester->getSampleLocalizedProductAttributeValues());

        $this->assertSame(['foo', 'bar'], $keys);
    }

    /**
     * @return void
     */
    public function testExtractValuesFromAttributes(): void
    {
        $values = $this->productAttributeFacade->extractValuesFromAttributes($this->tester->getSampleLocalizedProductAttributeValues());

        $expectedValues = [
            'Foo Value',
            '20 units',
            'Foo Value DE',
            'Foo Value US',
        ];

        sort($expectedValues);
        sort($values);

        $this->assertSame($expectedValues, $values);
    }

    /**
     * @return void
     */
    public function testGetUniqueSuperAttributesFromConcreteProducts(): void
    {
        $attributesData = $this->tester->createSampleAttributeMetadataWithSuperAttributeData();
        $productAbstractTransfer = $this->tester->createSampleAbstractProduct(ProductAttributeBusinessTester::ABSTRACT_SKU);
        $firstProductConcreteTransfer = $this->tester->createSampleProduct($productAbstractTransfer, ProductAttributeBusinessTester::CONCRETE_SKU, $attributesData);

        $this->tester->createSampleAttributeMetadata(ProductAttributeBusinessTester::ANOTHER_SUPER_ATTRIBUTE_KEY, true);

        $attributesData[ProductAttributeBusinessTester::SUPER_ATTRIBUTE_KEY] = ProductAttributeBusinessTester::SUPER_ATTRIBUTE_VALUE . 'new';
        $attributesData[ProductAttributeBusinessTester::ANOTHER_SUPER_ATTRIBUTE_KEY] = ProductAttributeBusinessTester::ANOTHER_SUPER_ATTRIBUTE_VALUE;
        $secondProductConcreteTransfer = $this->tester->createSampleProduct($productAbstractTransfer, ProductAttributeBusinessTester::CONCRETE_SKU . '2', $attributesData);

        $uniqueSuperAttributes = $this->productAttributeFacade->getUniqueSuperAttributesFromConcreteProducts([
            $firstProductConcreteTransfer,
            $secondProductConcreteTransfer,
        ]);

        $this->assertSame(2, count($uniqueSuperAttributes));
    }

    /**
     * @return \Spryker\Zed\ProductAttribute\Business\Model\Attribute\AttributeTranslator|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getAttributeTranslatorMock(): AttributeTranslator
    {
        $productManagementToLocaleBridgeMock = $this->getMockBuilder(ProductAttributeToLocaleBridge::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productManagementToGlossaryBridgeMock = $this->getMockBuilder(ProductAttributeToGlossaryBridge::class)
            ->disableOriginalConstructor()
            ->getMock();

        $glossaryKeyBuilderMock = $this->getMockBuilder(AttributeGlossaryKeyBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $attributeTranslatorMock = $this->getMockBuilder(AttributeTranslator::class)->setConstructorArgs([
            $this->productAttributeQueryContainer,
            $productManagementToLocaleBridgeMock,
            $productManagementToGlossaryBridgeMock,
            $glossaryKeyBuilderMock,
        ])->setMethods(['getLocaleByName'])->getMock();

        $attributeTranslatorMock->method('getLocaleByName')->willReturnCallback(function ($localeName) {
            return $this->tester->getLocale($localeName);
        });

        return $attributeTranslatorMock;
    }
}
