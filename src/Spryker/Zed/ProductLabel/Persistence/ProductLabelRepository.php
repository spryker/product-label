<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductLabel\Persistence;

use Generated\Shared\Transfer\FilterTransfer;
use Generated\Shared\Transfer\ProductLabelCriteriaTransfer;
use Generated\Shared\Transfer\ProductLabelTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Orm\Zed\ProductLabel\Persistence\Map\SpyProductLabelTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\ProductLabel\Persistence\ProductLabelPersistenceFactory getFactory()
 */
class ProductLabelRepository extends AbstractRepository implements ProductLabelRepositoryInterface
{
    /**
     * @param int $idProductLabel
     *
     * @return \Generated\Shared\Transfer\ProductLabelTransfer|null
     */
    public function findProductLabelById(int $idProductLabel): ?ProductLabelTransfer
    {
        $productLabelEntity = $this->getFactory()
            ->createProductLabelQuery()
            ->filterByIdProductLabel($idProductLabel)
            ->leftJoinWithProductLabelStore()
            ->leftJoinWithSpyProductLabelLocalizedAttributes()
            ->find()
            ->getFirst();

        if (!$productLabelEntity) {
            return null;
        }

        return $this->getFactory()
            ->createProductLabelMapper()
            ->mapProductLabelEntityToProductLabelTransfer($productLabelEntity, new ProductLabelTransfer());
    }

    /**
     * @param string $productLabelName
     *
     * @return \Generated\Shared\Transfer\ProductLabelTransfer|null
     */
    public function findProductLabelByName(string $productLabelName): ?ProductLabelTransfer
    {
        $productLabelEntity = $this->getFactory()
            ->createProductLabelQuery()
            ->filterByName($productLabelName)
            ->leftJoinWithProductLabelStore()
            ->leftJoinWithSpyProductLabelLocalizedAttributes()
            ->find()
            ->getFirst();

        if (!$productLabelEntity) {
            return null;
        }

        return $this->getFactory()
            ->createProductLabelMapper()
            ->mapProductLabelEntityToProductLabelTransfer($productLabelEntity, new ProductLabelTransfer());
    }

    /**
     * @return array<\Generated\Shared\Transfer\ProductLabelTransfer>
     */
    public function getAllProductLabelsSortedByPosition(): array
    {
        $productLabelEntities = $this->getFactory()
            ->createProductLabelQuery()
            ->leftJoinWithProductLabelStore()
            ->leftJoinWithSpyProductLabelLocalizedAttributes()
            ->orderByPosition(Criteria::ASC)
            ->find();

        return $this->getFactory()
            ->createProductLabelMapper()
            ->mapProductLabelEntitiesToProductLabelTransfers($productLabelEntities, []);
    }

    /**
     * @param int $idProductAbstract
     *
     * @return array<\Generated\Shared\Transfer\ProductLabelTransfer>
     */
    public function getProductLabelsByIdProductAbstract(int $idProductAbstract): array
    {
        $productLabelEntities = $this->getFactory()
            ->createProductLabelQuery()
            ->leftJoinWithProductLabelStore()
            ->leftJoinWithSpyProductLabelLocalizedAttributes()
            ->useSpyProductLabelProductAbstractQuery()
                ->filterByFkProductAbstract($idProductAbstract)
            ->endUse()
            ->find();

        return $this->getFactory()
            ->createProductLabelMapper()
            ->mapProductLabelEntitiesToProductLabelTransfers($productLabelEntities, []);
    }

    /**
     * @param \Generated\Shared\Transfer\ProductLabelCriteriaTransfer $productLabelCriteriaTransfer
     *
     * @return array<\Generated\Shared\Transfer\ProductLabelTransfer>
     */
    public function getActiveLabelsByCriteria(ProductLabelCriteriaTransfer $productLabelCriteriaTransfer): array
    {
        $productLabelQuery = $this->getFactory()->createProductLabelQuery();

        if ($productLabelCriteriaTransfer->getProductLabelIds()) {
            $productLabelQuery->filterByIdProductLabel_In($productLabelCriteriaTransfer->getProductLabelIds());
        }

        if ($productLabelCriteriaTransfer->getProductAbstractIds()) {
            $productLabelQuery->useSpyProductLabelProductAbstractQuery()
                    ->filterByFkProductAbstract_In($productLabelCriteriaTransfer->getProductAbstractIds())
                ->endUse();
        }

        if ($productLabelCriteriaTransfer->getStoreName() !== null) {
            $productLabelQuery->useProductLabelStoreQuery()
                    ->useStoreQuery()
                        ->filterByName($productLabelCriteriaTransfer->getStoreName())
                    ->endUse()
                ->endUse();
        }

        /** @var \Orm\Zed\ProductLabel\Persistence\SpyProductLabelQuery $productLabelQuery */
        $productLabelQuery = $productLabelQuery->joinWithSpyProductLabelLocalizedAttributes(Criteria::LEFT_JOIN)
            ->useSpyProductLabelLocalizedAttributesQuery(null, Criteria::LEFT_JOIN)
                ->joinSpyLocale()
            ->endUse()
            ->filterByIsActive(true);

        $productLabelEntities = $productLabelQuery
            ->filterByValidFrom('now', Criteria::LESS_EQUAL)
            ->_or()
            ->filterByValidFrom(null, Criteria::ISNULL)
            ->filterByValidTo('now', Criteria::GREATER_EQUAL)
            ->_or()
            ->filterByValidTo(null, Criteria::ISNULL)
            ->orderByIsExclusive(Criteria::DESC)
            ->orderByPosition(Criteria::ASC)
            ->find();

        return $this->getFactory()
            ->createProductLabelMapper()
            ->mapProductLabelEntitiesToProductLabelTransfers($productLabelEntities, []);
    }

    /**
     * @param int $idProductAbstract
     *
     * @return array<int>
     */
    public function getProductLabelIdsByIdProductAbstract(int $idProductAbstract): array
    {
        $productLabelEntities = $this->getFactory()
            ->createProductLabelQuery()
            ->useSpyProductLabelProductAbstractQuery()
                ->filterByFkProductAbstract($idProductAbstract)
            ->endUse()
            ->select(SpyProductLabelTableMap::COL_ID_PRODUCT_LABEL)
            ->find();

        return $productLabelEntities->getData();
    }

    /**
     * @param int $idProductAbstract
     *
     * @return array<int>
     */
    public function getActiveProductLabelIdsByIdProductAbstract(int $idProductAbstract): array
    {
        $productLabelEntities = $this->getFactory()
            ->createProductLabelQuery()
            ->filterByIsActive(true)
            ->useSpyProductLabelProductAbstractQuery()
                ->filterByFkProductAbstract($idProductAbstract)
            ->endUse()
            ->select(SpyProductLabelTableMap::COL_ID_PRODUCT_LABEL)
            ->find();

        return $productLabelEntities->getData();
    }

    /**
     * @param int $idProductLabel
     *
     * @return \Generated\Shared\Transfer\StoreRelationTransfer
     */
    public function getStoreRelationByIdProductLabel(int $idProductLabel): StoreRelationTransfer
    {
        $productLabelStoreEntities = $this->getFactory()
            ->createProductLabelStoreQuery()
            ->filterByFkProductLabel($idProductLabel)
            ->leftJoinWithStore()
            ->find();

        $storeRelationTransfer = (new StoreRelationTransfer())->setIdEntity($idProductLabel);

        return $this->getFactory()
            ->createProductLabelStoreRelationMapper()
            ->mapProductLabelStoreEntitiesToStoreRelationTransfer($productLabelStoreEntities, $storeRelationTransfer);
    }

    /**
     * @param array<int> $productAbstractIds
     *
     * @return array<\Generated\Shared\Transfer\ProductLabelProductAbstractTransfer>
     */
    public function getProductLabelProductAbstractsByProductAbstractIds(array $productAbstractIds): array
    {
        $productLabelProductAbstractEntities = $this->getFactory()
            ->createProductRelationQuery()
            ->filterByFkProductAbstract_In($productAbstractIds)
            ->joinWithSpyProductLabel()
            ->orderBy(SpyProductLabelTableMap::COL_POSITION)
            ->find();

        if (!$productLabelProductAbstractEntities->count()) {
            return [];
        }

        return $this->getFactory()
            ->createProductLabelProductAbstractMapper()
            ->mapProductLabelProductAbstractEntitiesToProductLabelProductTransfers($productLabelProductAbstractEntities, []);
    }

    /**
     * @param \Generated\Shared\Transfer\FilterTransfer $filterTransfer
     *
     * @return array<\Generated\Shared\Transfer\ProductLabelProductAbstractTransfer>
     */
    public function getProductLabelProductAbstractsByFilter(FilterTransfer $filterTransfer): array
    {
        $productLabelProductAbstractEntities = $this->getFactory()
            ->createProductRelationQuery()
            ->setLimit($filterTransfer->getLimit())
            ->setOffset($filterTransfer->getOffset())
            ->find();

        if (!$productLabelProductAbstractEntities->count()) {
            return [];
        }

        return $this->getFactory()
            ->createProductLabelProductAbstractMapper()
            ->mapProductLabelProductAbstractEntitiesToProductLabelProductTransfers($productLabelProductAbstractEntities, []);
    }

    /**
     * @param int $idProductLabel
     * @param array<int> $productAbstractIds
     *
     * @return array<\Generated\Shared\Transfer\ProductLabelProductAbstractTransfer>
     */
    public function getProductAbstractRelationsByIdProductLabelAndProductAbstractIds(int $idProductLabel, array $productAbstractIds): array
    {
        $productLabelProductAbstractEntities = $this->getFactory()
            ->createProductRelationQuery()
            ->filterByFkProductLabel($idProductLabel)
            ->filterByFkProductAbstract_In($productAbstractIds)
            ->find();

        if (!$productLabelProductAbstractEntities->count()) {
            return [];
        }

        return $this->getFactory()
            ->createProductLabelProductAbstractMapper()
            ->mapProductLabelProductAbstractEntitiesToProductLabelProductTransfers($productLabelProductAbstractEntities, []);
    }

    /**
     * @param int $idProductAbstract
     *
     * @return bool
     */
    public function checkProductLabelProductAbstractByIdProductAbstractExists(int $idProductAbstract): bool
    {
        return $this->getFactory()
            ->createProductLabelQuery()
            ->useSpyProductLabelProductAbstractQuery()
                ->filterByFkProductAbstract($idProductAbstract)
            ->endUse()
            ->exists();
    }
}
