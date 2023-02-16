<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductLabel\Persistence;

use Generated\Shared\Transfer\PaginationTransfer;
use Generated\Shared\Transfer\ProductLabelCollectionTransfer;
use Generated\Shared\Transfer\ProductLabelCriteriaTransfer;
use Generated\Shared\Transfer\ProductLabelTransfer;
use Orm\Zed\ProductLabel\Persistence\SpyProductLabelQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\ObjectCollection;
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
            ->findOne();

        if (!$productLabelEntity) {
            return null;
        }

        return $this->getFactory()
            ->createProductLabelMapper()
            ->mapProductLabelEntityToProductLabelTransfer($productLabelEntity, new ProductLabelTransfer());
    }

    /**
     * @param \Generated\Shared\Transfer\ProductLabelCriteriaTransfer $productLabelCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\ProductLabelCollectionTransfer
     */
    public function getProductLabelCollection(ProductLabelCriteriaTransfer $productLabelCriteriaTransfer): ProductLabelCollectionTransfer
    {
        $productLabelCollectionTransfer = new ProductLabelCollectionTransfer();
        $productLabelQuery = $this->getFactory()->createProductLabelQuery();

        $paginationTransfer = $productLabelCriteriaTransfer->getPagination();
        if ($paginationTransfer) {
            $productLabelQuery = $this->applyProductLabelPagination($productLabelQuery, $paginationTransfer);
            $productLabelCollectionTransfer->setPagination($paginationTransfer);
        }

        $productLabelQuery = $this->applyProductLabelConditions($productLabelQuery, $productLabelCriteriaTransfer);
        $productLabelQuery = $this->applyProductLabelSorting($productLabelQuery, $productLabelCriteriaTransfer);

        $productLabelEntities = $productLabelQuery->find();
        $productLabelEntitiesIndexedByProductLabelIds = $this->indexProductLabelEntitiesByProductLabelIds($productLabelEntities);

        $this->expandProductLabelWithProductLabelLocalizedAttributes($productLabelEntitiesIndexedByProductLabelIds, $productLabelCriteriaTransfer);
        $this->expandProductLabelWithProductLabelProductAbstracts($productLabelEntitiesIndexedByProductLabelIds, $productLabelCriteriaTransfer);

        return $this->getFactory()
            ->createProductLabelMapper()
            ->mapProductLabelEntitiesToProductLabelCollectionTransfer(
                $productLabelEntities,
                $productLabelCollectionTransfer
            );
    }

    /**
     * @param \Orm\Zed\ProductLabel\Persistence\SpyProductLabelQuery $productLabelQuery
     * @param \Generated\Shared\Transfer\PaginationTransfer $paginationTransfer
     *
     * @return \Orm\Zed\ProductLabel\Persistence\SpyProductLabelQuery
     */
    protected function applyProductLabelPagination(
        SpyProductLabelQuery $productLabelQuery,
        PaginationTransfer $paginationTransfer
    ): SpyProductLabelQuery {
        $paginationTransfer->setNbResults($productLabelQuery->count());

        if ($paginationTransfer->getLimit() !== null && $paginationTransfer->getOffset() !== null) {
            return $productLabelQuery
                ->limit($paginationTransfer->getLimit())
                ->offset($paginationTransfer->getOffset());
        }

        return $productLabelQuery;
    }

    /**
     * @param \Orm\Zed\ProductLabel\Persistence\SpyProductLabelQuery $productLabelQuery
     * @param \Generated\Shared\Transfer\ProductLabelCriteriaTransfer $productLabelCriteriaTransfer
     *
     * @return \Orm\Zed\ProductLabel\Persistence\SpyProductLabelQuery
     */
    protected function applyProductLabelConditions(
        SpyProductLabelQuery $productLabelQuery,
        ProductLabelCriteriaTransfer $productLabelCriteriaTransfer
    ): SpyProductLabelQuery {
        $productLabelConditions = $productLabelCriteriaTransfer->getProductLabelConditions();
        if (!$productLabelConditions) {
            return $productLabelQuery;
        }

        if ($productLabelConditions->getProductAbstractIds()) {
            $productLabelQuery->useSpyProductLabelProductAbstractQuery()
                ->filterByFkProductAbstract_In(
                    $productLabelConditions->getProductAbstractIds())
                ->endUse();
        }

        if ($productLabelConditions->getIsActive()) {
            $productLabelQuery->filterByIsActive(true)
                ->filterByValidFrom('now', Criteria::LESS_EQUAL)
                ->_or()
                ->filterByValidFrom(null, Criteria::ISNULL)
                ->filterByValidTo('now', Criteria::GREATER_EQUAL)
                ->_or()
                ->filterByValidTo(null, Criteria::ISNULL);
        }

        return $productLabelQuery;
    }

    /**
     * @param \Orm\Zed\ProductLabel\Persistence\SpyProductLabelQuery $productLabelQuery
     * @param \Generated\Shared\Transfer\ProductLabelCriteriaTransfer $productLabelCriteriaTransfer
     *
     * @return \Orm\Zed\ProductLabel\Persistence\SpyProductLabelQuery
     */
    protected function applyProductLabelSorting(
        SpyProductLabelQuery $productLabelQuery,
        ProductLabelCriteriaTransfer $productLabelCriteriaTransfer
    ): SpyProductLabelQuery {
        $sortCollection = $productLabelCriteriaTransfer->getSortCollection();
        foreach ($sortCollection as $sortTransfer) {
            $productLabelQuery->orderBy(
                $sortTransfer->getFieldOrFail(),
                $sortTransfer->getIsAscending() ? Criteria::ASC : Criteria::DESC
            );
        }

        return $productLabelQuery;
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection<\Orm\Zed\ProductLabel\Persistence\SpyProductLabel> $productLabelEntities
     *
     * @return array<int, \Orm\Zed\ProductLabel\Persistence\SpyProductLabel>
     */
    protected function indexProductLabelEntitiesByProductLabelIds(ObjectCollection $productLabelEntities): array
    {
        $productLabelEntitiesIndexedByProductLabelIds = [];
        foreach ($productLabelEntities as $productLabelEntity) {
            $productLabelEntitiesIndexedByProductLabelIds[$productLabelEntity->getIdProductLabel()] = $productLabelEntity;
        }

        return $productLabelEntitiesIndexedByProductLabelIds;
    }

    /**
     * @param array<int, \Orm\Zed\ProductLabel\Persistence\SpyProductLabel> $productLabelEntitiesIndexedByProductLabelIds
     * @param \Generated\Shared\Transfer\ProductLabelCriteriaTransfer $productLabelCriteriaTransfer
     *
     * @return array<int, \Orm\Zed\ProductLabel\Persistence\SpyProductLabel>
     */
    protected function expandProductLabelWithProductLabelLocalizedAttributes(
        array $productLabelEntitiesIndexedByProductLabelIds,
        ProductLabelCriteriaTransfer $productLabelCriteriaTransfer
    ): array {
        foreach ($productLabelEntitiesIndexedByProductLabelIds as $productLabelEntity) {
            $productLabelEntity->initSpyProductLabelLocalizedAttributess(false);
        }

        if (!$productLabelCriteriaTransfer->getWithProductLabelLocalizedAttributes()) {
            return $productLabelEntitiesIndexedByProductLabelIds;
        }

        $productLabelLocalizedAttributeEntities = $this->getFactory()
            ->createLocalizedAttributesQuery()
            ->leftJoinWithSpyLocale()
            ->leftJoinWithSpyProductLabel()
            ->filterByFkProductLabel_In(array_keys($productLabelEntitiesIndexedByProductLabelIds))
            ->find();

        foreach ($productLabelLocalizedAttributeEntities as $productLabelLocalizedAttributeEntity) {
            $productLabelId = $productLabelLocalizedAttributeEntity->getFkProductLabel();
            if (!isset($productLabelEntitiesIndexedByProductLabelIds[$productLabelId])) {
                continue;
            }

            $productLabelEntitiesIndexedByProductLabelIds[$productLabelId]->addSpyProductLabelLocalizedAttributes($productLabelLocalizedAttributeEntity);
        }

        return $productLabelEntitiesIndexedByProductLabelIds;
    }

    /**
     * @param array<int, \Orm\Zed\ProductLabel\Persistence\SpyProductLabel> $productLabelEntitiesIndexedByProductLabelIds
     * @param \Generated\Shared\Transfer\ProductLabelCriteriaTransfer $productLabelCriteriaTransfer
     *
     * @return array<int, \Orm\Zed\ProductLabel\Persistence\SpyProductLabel>
     */
    protected function expandProductLabelWithProductLabelProductAbstracts(
        array $productLabelEntitiesIndexedByProductLabelIds,
        ProductLabelCriteriaTransfer $productLabelCriteriaTransfer
    ): array {
        foreach ($productLabelEntitiesIndexedByProductLabelIds as $productLabelEntity) {
            $productLabelEntity->initSpyProductLabelProductAbstracts(false);
        }

        if (!$productLabelCriteriaTransfer->getWithProductLabelProductAbstracts()) {
            return $productLabelEntitiesIndexedByProductLabelIds;
        }

        $productLabelProductAbstractEntities = $this->getFactory()
            ->createProductRelationQuery()
            ->filterByFkProductLabel_In(array_keys($productLabelEntitiesIndexedByProductLabelIds))
            ->find();

        foreach ($productLabelProductAbstractEntities as $productLabelProductAbstractEntity) {
            $productLabelId = $productLabelProductAbstractEntity->getFkProductLabel();
            if (!isset($productLabelEntitiesIndexedByProductLabelIds[$productLabelId])) {
                continue;
            }

            $productLabelEntitiesIndexedByProductLabelIds[$productLabelId]->addSpyProductLabelProductAbstract($productLabelProductAbstractEntity);
        }

        return $productLabelEntitiesIndexedByProductLabelIds;
    }
}
