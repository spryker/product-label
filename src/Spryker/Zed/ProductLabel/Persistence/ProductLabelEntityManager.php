<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductLabel\Persistence;

use Generated\Shared\Transfer\ProductLabelTransfer;
use Orm\Zed\ProductLabel\Persistence\SpyProductLabel;
use Orm\Zed\ProductLabel\Persistence\SpyProductLabelStore;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;
use Spryker\Zed\ProductLabel\Persistence\Exception\MissingProductLabelException;

/**
 * @method \Spryker\Zed\ProductLabel\Persistence\ProductLabelPersistenceFactory getFactory()
 */
class ProductLabelEntityManager extends AbstractEntityManager implements ProductLabelEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\ProductLabelTransfer $productLabelTransfer
     *
     * @return \Generated\Shared\Transfer\ProductLabelTransfer
     */
    public function createProductLabel(ProductLabelTransfer $productLabelTransfer): ProductLabelTransfer
    {
        $productLabelMapper = $this->getFactory()->createProductLabelMapper();

        $productLabelEntity = $productLabelMapper->mapProductLabelTransferToProductLabelEntity(
            $productLabelTransfer,
            new SpyProductLabel(),
        );
        $productLabelEntity->save();

        return $productLabelMapper->mapProductLabelEntityToProductLabelTransfer(
            $productLabelEntity,
            $productLabelTransfer,
        );
    }

    /**
     * @param \Generated\Shared\Transfer\ProductLabelTransfer $productLabelTransfer
     *
     * @throws \Spryker\Zed\ProductLabel\Persistence\Exception\MissingProductLabelException
     *
     * @return array<string>
     */
    public function updateProductLabel(ProductLabelTransfer $productLabelTransfer): array
    {
        $productLabelEntity = $this->getFactory()
            ->createProductLabelQuery()
            ->findOneByIdProductLabel($productLabelTransfer->getIdProductLabel());

        if ($productLabelEntity === null) {
            throw new MissingProductLabelException(sprintf(
                'Could not find product label for id "%s"',
                $productLabelTransfer->getIdProductLabel(),
            ));
        }

        $productLabelMapper = $this->getFactory()->createProductLabelMapper();

        $productLabelEntity = $productLabelMapper->mapProductLabelTransferToProductLabelEntity(
            $productLabelTransfer,
            $productLabelEntity,
        );

        $modifiedColumns = $productLabelEntity->getModifiedColumns();

        $productLabelEntity->save();

        return $modifiedColumns;
    }

    /**
     * @param int $idProductLabel
     *
     * @return void
     */
    public function deleteProductLabel(int $idProductLabel): void
    {
        /** @var \Propel\Runtime\Collection\ObjectCollection $productLabelCollection */
        $productLabelCollection = $this->getFactory()
            ->createProductLabelQuery()
            ->findByIdProductLabel($idProductLabel);

        $productLabelCollection->delete();
    }

    /**
     * @param int $idProductLabel
     *
     * @return void
     */
    public function deleteProductLabelStoreRelations(int $idProductLabel): void
    {
        /** @var \Propel\Runtime\Collection\ObjectCollection $productLabelStoreCollection */
        $productLabelStoreCollection = $this->getFactory()
            ->createProductLabelStoreQuery()
            ->findByFkProductLabel($idProductLabel);

        $productLabelStoreCollection->delete();
    }

    /**
     * @param int $idProductLabel
     *
     * @return void
     */
    public function deleteProductLabelLocalizedAttributes(int $idProductLabel): void
    {
        /** @var \Propel\Runtime\Collection\ObjectCollection $productLocalizedAttributesCollection */
        $productLocalizedAttributesCollection = $this->getFactory()
            ->createLocalizedAttributesQuery()
            ->findByFkProductLabel($idProductLabel);

        $productLocalizedAttributesCollection->delete();
    }

    /**
     * @param int $idProductLabel
     * @param array<int> $productAbstractIds
     *
     * @return void
     */
    public function deleteProductLabelProductAbstractRelations(int $idProductLabel, array $productAbstractIds = []): void
    {
        $productRelationQuery = $this->getFactory()
            ->createProductRelationQuery()
            ->filterByFkProductLabel($idProductLabel);

        if ($productAbstractIds) {
            $productRelationQuery->filterByFkProductAbstract_In($productAbstractIds);
        }

        $productRelationQuery->find()->delete();
    }

    /**
     * @param array<int> $idStores
     * @param int $idProductLabel
     *
     * @return void
     */
    public function removeProductLabelStoreRelationForStores(array $idStores, int $idProductLabel): void
    {
        if ($idStores === []) {
            return;
        }

        $this->getFactory()
            ->createProductLabelStoreQuery()
            ->filterByFkProductLabel($idProductLabel)
            ->filterByFkStore_In($idStores)
            ->find()
            ->delete();
    }

    /**
     * @param array<int> $idStores
     * @param int $idProductLabel
     *
     * @return void
     */
    public function createProductLabelStoreRelationForStores(array $idStores, int $idProductLabel): void
    {
        foreach ($idStores as $idStore) {
            $productLabelStoreEntity = new SpyProductLabelStore();
            $productLabelStoreEntity->setFkStore($idStore)
                ->setFkProductLabel($idProductLabel)
                ->save();
        }
    }
}
