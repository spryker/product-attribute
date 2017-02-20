<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\Cms\Business\Page;

use Generated\Shared\Transfer\CmsPageAttributesTransfer;
use Generated\Shared\Transfer\CmsPageMetaAttributesTransfer;
use Generated\Shared\Transfer\CmsPageTransfer;
use Orm\Zed\Url\Persistence\SpyUrl;
use Spryker\Zed\CmsGui\Dependency\QueryContainer\CmsGuiToCmsQueryContainerInterface;
use Spryker\Zed\Cms\Business\Page\CmsPageSaver;
use Spryker\Zed\Cms\Business\Page\CmsPageUrlBuilderInterface;
use Spryker\Zed\Cms\Dependency\Facade\CmsToTouchInterface;
use Spryker\Zed\Url\Business\UrlFacadeInterface;
use Unit\Spryker\Zed\Cms\Business\CmsMocks;

/**
 * @group Unit
 * @group Spryker
 * @group Zed
 * @group Cms
 * @group Business
 * @group Page
 * @group CmsPageSaverTest
 */
class CmsPageSaverTest extends CmsMocks
{

    /**
     * @return void
     */
    public function testCreatePageShoulPersistGivenTransfer()
    {
        $cmsPageSaverMock = $this->createCmsPageSaverMock();

        $cmsPageEntityMock = $this->createCmsPageEntityMock();
        $cmsPageEntityMock->setIdCmsPage(1);
        $cmsPageEntityMock->expects($this->once())
            ->method('save');
        $cmsPageSaverMock->expects($this->once())
            ->method('createCmsPageEntity')
            ->willReturn($cmsPageEntityMock);

        $cmsPageLocalizedAttributesEntityMock = $this->createCmsPageLocalizedAttributesEntityMock();
        $cmsPageLocalizedAttributesEntityMock->expects($this->exactly(2))
            ->method('save');
        $cmsPageSaverMock->expects($this->once())
            ->method('createCmsPageLocalizedAttributesEntity')
            ->willReturn($cmsPageLocalizedAttributesEntityMock);

        $cmsPageTransfer = new CmsPageTransfer();
        $cmsPageTransfer->addPageAttribute(new CmsPageAttributesTransfer());
        $cmsPageTransfer->addMetaAttribute(new CmsPageMetaAttributesTransfer());

        $idCmsPage = $cmsPageSaverMock->createPage($cmsPageTransfer);

        $this->assertEquals($cmsPageEntityMock->getIdCmsPage(), $idCmsPage);
    }

    /**
     * @return void
     */
    public function testUpdatePageShouldUpdateExistingEnityWithNewData()
    {
        $touchFacadeMock = $this->createTouchFacadeMock();
        $touchFacadeMock->expects($this->once())
            ->method('touchActive');

        $cmsPageSaverMock = $this->createCmsPageSaverMock(null, $touchFacadeMock);

        $cmsPageEntityMock = $this->createCmsPageEntityMock();
        $cmsPageEntityMock->setIsActive(true);

        $localizedAttributesEntityMock = $this->createCmsPageLocalizedAttributesEntityMock();
        $localizedAttributesEntityMock->setIdCmsPageLocalizedAttributes(1);
        $cmsPageEntityMock->addSpyCmsPageLocalizedAttributes($localizedAttributesEntityMock);

        $urlEntity = new SpyUrl();
        $urlEntity->setFkLocale(1);
        $cmsPageEntityMock->addSpyUrl($urlEntity);

        $cmsPageSaverMock->expects($this->once())
            ->method('getCmsPageEntity')
            ->willReturn($cmsPageEntityMock);

        $cmsPageTransfer = new CmsPageTransfer();
        $cmsPageTransfer->setIsSearchable(false);

        $cmsPageAttributesTransfer = new CmsPageAttributesTransfer();
        $cmsPageAttributesTransfer->setIdCmsPageLocalizedAttributes(1);
        $cmsPageAttributesTransfer->setUrl('/en/english');
        $cmsPageAttributesTransfer->setName('english name');
        $cmsPageAttributesTransfer->setFkLocale(1);

        $cmsPageTransfer->addPageAttribute($cmsPageAttributesTransfer);

        $cmsPageSaverMock->updatePage($cmsPageTransfer);

        $cmsPageAttributesEntity = $cmsPageEntityMock->getSpyCmsPageLocalizedAttributess()[0];
        $this->assertEquals($cmsPageAttributesEntity->getName(), $cmsPageAttributesTransfer->getName());
        $this->assertEquals($urlEntity->getUrl(), $cmsPageAttributesTransfer->getUrlPrefix());
    }

    /**
     * @param \Spryker\Zed\Url\Business\UrlFacadeInterface|null $urlFacadeMock
     * @param \Spryker\Zed\Cms\Dependency\Facade\CmsToTouchInterface|null $touchFacadeMock
     * @param \Spryker\Zed\CmsGui\Dependency\QueryContainer\CmsGuiToCmsQueryContainerInterface|null $cmsQueryContainerMock
     * @param \Spryker\Zed\Cms\Business\Page\CmsPageUrlBuilderInterface|null $cmsPageUrlBuilderMock
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Zed\Cms\Business\Page\CmsPageSaver
     */
    protected function createCmsPageSaverMock(
        UrlFacadeInterface $urlFacadeMock = null,
        CmsToTouchInterface $touchFacadeMock = null,
        CmsGuiToCmsQueryContainerInterface $cmsQueryContainerMock = null,
        CmsPageUrlBuilderInterface $cmsPageUrlBuilderMock = null
    ) {

        if ($urlFacadeMock === null) {
            $urlFacadeMock = $this->createUrlFacadeMock();
        }

        if ($touchFacadeMock === null) {
            $touchFacadeMock = $this->createTouchFacadeMock();
        }

        if ($cmsQueryContainerMock === null) {
            $cmsQueryContainerMock = $this->createCmsQueryContainerMock();
        }

        if ($cmsPageUrlBuilderMock === null) {
            $cmsPageUrlBuilderMock = $this->createCmsPageUrlBuilderMock();
        }

        return $this->getMockBuilder(CmsPageSaver::class)
            ->setConstructorArgs([
                $urlFacadeMock,
                $touchFacadeMock,
                $cmsQueryContainerMock,
                $cmsPageUrlBuilderMock,
            ])
            ->setMethods([
                'getCmsPageEntity',
                'createCmsPageEntity',
                'createCmsPageLocalizedAttributesEntity',
            ])
            ->getMock();
    }

}