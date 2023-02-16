<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductLabel\Persistence\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\ProductLabelCollectionTransfer;
use Generated\Shared\Transfer\ProductLabelTransfer;
use Orm\Zed\ProductLabel\Persistence\SpyProductLabel;
use Propel\Runtime\Collection\ObjectCollection;

class ProductLabelMapper
{
    /**
     * @var string
     */
    protected const VALIDITY_DATE_FORMAT = 'Y-m-d';

    /**
     * @var \Spryker\Zed\ProductLabel\Persistence\Mapper\ProductLabelLocalizedAttributesMapper
     */
    protected $productLabelLocalizedAttributesMapper;

    /**
     * @var \Spryker\Zed\ProductLabel\Persistence\Mapper\ProductLabelProductAbstractMapper
     */
    protected $productLabelProductAbstractMapper;

    /**
     * @param \Spryker\Zed\ProductLabel\Persistence\Mapper\ProductLabelLocalizedAttributesMapper $productLabelLocalizedAttributesMapper
     * @param \Spryker\Zed\ProductLabel\Persistence\Mapper\ProductLabelProductAbstractMapper $productLabelProductAbstractMapper
     */
    public function __construct(
        ProductLabelLocalizedAttributesMapper $productLabelLocalizedAttributesMapper,
        ProductLabelProductAbstractMapper $productLabelProductAbstractMapper
    ) {
        $this->productLabelLocalizedAttributesMapper = $productLabelLocalizedAttributesMapper;
        $this->productLabelProductAbstractMapper = $productLabelProductAbstractMapper;
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection<\Orm\Zed\ProductLabel\Persistence\SpyProductLabel> $productLabelEntities
     * @param array<\Generated\Shared\Transfer\ProductLabelTransfer> $productLabelTransfers
     *
     * @return array<\Generated\Shared\Transfer\ProductLabelTransfer>
     */
    public function mapProductLabelEntitiesToProductLabelTransfers(
        ObjectCollection $productLabelEntities,
        array $productLabelTransfers
    ): array {
        foreach ($productLabelEntities as $productLabelEntity) {
            $productLabelTransfers[] = $this->mapProductLabelEntityToProductLabelTransfer(
                $productLabelEntity,
                new ProductLabelTransfer()
            );
        }

        return $productLabelTransfers;
    }

    /**
     * @param \Orm\Zed\ProductLabel\Persistence\SpyProductLabel $productLabelEntity
     * @param \Generated\Shared\Transfer\ProductLabelTransfer $productLabelTransfer
     *
     * @return \Generated\Shared\Transfer\ProductLabelTransfer
     */
    public function mapProductLabelEntityToProductLabelTransfer(
        SpyProductLabel $productLabelEntity,
        ProductLabelTransfer $productLabelTransfer
    ): ProductLabelTransfer {
        $productLabelTransfer->fromArray($productLabelEntity->toArray(), true);

        $productLabelTransfer->setValidFrom(
            $productLabelEntity->getValidFrom(static::VALIDITY_DATE_FORMAT)
        );
        $productLabelTransfer->setValidTo(
            $productLabelEntity->getValidTo(static::VALIDITY_DATE_FORMAT)
        );

        $productLabelEntity->initSpyProductLabelLocalizedAttributess(false);

        $productLabelLocalizedAttributesTransfers = $this->productLabelLocalizedAttributesMapper
            ->mapProductLabelLocalizedAttributesEntitiesToProductLabelLocalizedAttributesTransfers(
                $productLabelEntity->getSpyProductLabelLocalizedAttributess(),
                $productLabelTransfer->getLocalizedAttributesCollection()
            );
        $productLabelTransfer->setLocalizedAttributesCollection(
            new ArrayObject($productLabelLocalizedAttributesTransfers)
        );

        $productLabelProductAbstractTransfers = $this->productLabelProductAbstractMapper
            ->mapProductLabelProductAbstractEntitiesToProductLabelProductTransfers(
                $productLabelEntity->getSpyProductLabelProductAbstracts(),
                []
            );
        $productLabelTransfer->setProductLabelProductAbstracts(
            new ArrayObject($productLabelProductAbstractTransfers)
        );

        return $productLabelTransfer;
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection<\Orm\Zed\ProductLabel\Persistence\SpyProductLabel> $productLabelEntities
     * @param \Generated\Shared\Transfer\ProductLabelCollectionTransfer $productLabelCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\ProductLabelCollectionTransfer
     */
    public function mapProductLabelEntitiesToProductLabelCollectionTransfer(
        ObjectCollection $productLabelEntities,
        ProductLabelCollectionTransfer $productLabelCollectionTransfer
    ): ProductLabelCollectionTransfer {
        foreach ($productLabelEntities as $productLabelEntity) {
            $productLabelCollectionTransfer->addProductLabel(
                $this->mapProductLabelEntityToProductLabelTransfer($productLabelEntity, new ProductLabelTransfer())
            );
        }

        return $productLabelCollectionTransfer;
    }
}
