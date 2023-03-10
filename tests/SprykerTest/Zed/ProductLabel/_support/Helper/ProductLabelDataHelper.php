<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductLabel\Helper;

use Codeception\Module;
use Generated\Shared\DataBuilder\ProductLabelBuilder;
use Generated\Shared\DataBuilder\ProductLabelLocalizedAttributesBuilder;
use Generated\Shared\Transfer\ProductLabelLocalizedAttributesTransfer;
use Generated\Shared\Transfer\ProductLabelTransfer;
use Orm\Zed\ProductLabel\Persistence\SpyProductLabelQuery;
use Spryker\Zed\ProductLabel\Business\ProductLabelFacadeInterface;
use SprykerTest\Shared\Testify\Helper\DataCleanupHelperTrait;
use SprykerTest\Shared\Testify\Helper\LocatorHelperTrait;

class ProductLabelDataHelper extends Module
{
    use LocatorHelperTrait;
    use DataCleanupHelperTrait;

    /**
     * @param array $seedData
     *
     * @return \Generated\Shared\Transfer\ProductLabelTransfer
     */
    public function haveProductLabel(array $seedData = []): ProductLabelTransfer
    {
        /** @var \Generated\Shared\Transfer\ProductLabelTransfer $productLabelTransfer */
        $productLabelTransfer = (new ProductLabelBuilder($seedData + [
                ProductLabelTransfer::VALID_FROM => null,
                ProductLabelTransfer::VALID_TO => null,
            ]))->build();
        $productLabelTransfer->setIdProductLabel(null);
        $productLabelTransfer->setPosition($seedData[ProductLabelTransfer::POSITION] ?? 0);

        foreach ($this->getLocator()->locale()->facade()->getLocaleCollection() as $localeTransfer) {
            $productLabelLocalizedAttributesTransfer = (new ProductLabelLocalizedAttributesBuilder([
                ProductLabelLocalizedAttributesTransfer::FK_LOCALE => $localeTransfer->getIdLocale(),
            ]))->build();
            $productLabelTransfer->addLocalizedAttributes($productLabelLocalizedAttributesTransfer);
        }

        $this->getProductLabelFacade()->createLabel($productLabelTransfer);

        $productLabelTransfer = $this->getProductLabelFacade()->findLabelByLabelName($productLabelTransfer->getName());

        $this->getDataCleanupHelper()->_addCleanup(function () use ($productLabelTransfer): void {
            $this->getProductLabelFacade()->removeLabel($productLabelTransfer);
        });

        return $productLabelTransfer;
    }

    /**
     * @param int $idProductLabel
     * @param int $idProductAbstract
     *
     * @return void
     */
    public function haveProductLabelToAbstractProductRelation(int $idProductLabel, int $idProductAbstract): void
    {
        $this->getProductLabelFacade()
            ->addAbstractProductRelationsForLabel($idProductLabel, [$idProductAbstract]);

        $this->getDataCleanupHelper()->_addCleanup(function () use ($idProductLabel, $idProductAbstract): void {
            $this->getProductLabelFacade()->removeProductAbstractRelationsForLabel(
                $idProductLabel,
                [$idProductAbstract],
            );
        });
    }

    /**
     * @return void
     */
    public function ensureProductLabelTableIsEmpty(): void
    {
        SpyProductLabelQuery::create()->deleteAll();
    }

    /**
     * @return \Spryker\Zed\ProductLabel\Business\ProductLabelFacadeInterface
     */
    protected function getProductLabelFacade(): ProductLabelFacadeInterface
    {
        return $this->getLocator()->productLabel()->facade();
    }
}
