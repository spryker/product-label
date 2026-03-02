<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductLabel\Persistence;

use Generated\Shared\Transfer\FilterTransfer;
use Generated\Shared\Transfer\ProductLabelCollectionTransfer;
use Generated\Shared\Transfer\ProductLabelCriteriaTransfer;
use Generated\Shared\Transfer\ProductLabelTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;

interface ProductLabelRepositoryInterface
{
    public function findProductLabelById(int $idProductLabel): ?ProductLabelTransfer;

    /**
     * @param array<int> $productAbstractIds
     *
     * @return array<\Generated\Shared\Transfer\ProductLabelProductAbstractTransfer>
     */
    public function getProductLabelProductAbstractsByProductAbstractIds(array $productAbstractIds): array;

    /**
     * @param \Generated\Shared\Transfer\FilterTransfer $filterTransfer
     *
     * @return array<\Generated\Shared\Transfer\ProductLabelProductAbstractTransfer>
     */
    public function getProductLabelProductAbstractsByFilter(FilterTransfer $filterTransfer): array;

    public function findProductLabelByName(string $productLabelName): ?ProductLabelTransfer;

    /**
     * @return array<\Generated\Shared\Transfer\ProductLabelTransfer>
     */
    public function getAllProductLabelsSortedByPosition(): array;

    /**
     * @param int $idProductAbstract
     *
     * @return array<\Generated\Shared\Transfer\ProductLabelTransfer>
     */
    public function getProductLabelsByIdProductAbstract(int $idProductAbstract): array;

    /**
     * @deprecated Use {@link \Spryker\Zed\ProductLabel\Persistence\ProductLabelRepositoryInterface::getProductLabelCollection()} instead.
     *
     * @param \Generated\Shared\Transfer\ProductLabelCriteriaTransfer $productLabelCriteriaTransfer
     *
     * @return array<\Generated\Shared\Transfer\ProductLabelTransfer>
     */
    public function getActiveLabelsByCriteria(ProductLabelCriteriaTransfer $productLabelCriteriaTransfer): array;

    public function getProductLabelIdsByIdProductAbstract(int $idProductAbstract): array;

    /**
     * @param int $idProductAbstract
     *
     * @return array<int>
     */
    public function getActiveProductLabelIdsByIdProductAbstract(int $idProductAbstract): array;

    public function getStoreRelationByIdProductLabel(int $idProductLabel): StoreRelationTransfer;

    /**
     * @param int $idProductLabel
     * @param array<int> $productAbstractIds
     *
     * @return array<\Generated\Shared\Transfer\ProductLabelProductAbstractTransfer>
     */
    public function getProductAbstractRelationsByIdProductLabelAndProductAbstractIds(int $idProductLabel, array $productAbstractIds): array;

    public function checkProductLabelProductAbstractByIdProductAbstractExists(int $idProductAbstract): bool;

    public function getProductLabelCollection(ProductLabelCriteriaTransfer $productLabelCriteriaTransfer): ProductLabelCollectionTransfer;

    /**
     * @param array<int> $productLabelIds
     *
     * @return array<\Generated\Shared\Transfer\ProductLabelProductAbstractTransfer>
     */
    public function getProductAbstractRelationsByIdProductLabelIds(array $productLabelIds): array;
}
