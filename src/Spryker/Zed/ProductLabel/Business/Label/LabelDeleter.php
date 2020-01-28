<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductLabel\Business\Label;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\ProductLabelResponseTransfer;
use Generated\Shared\Transfer\ProductLabelTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Spryker\Zed\ProductLabel\Persistence\ProductLabelEntityManagerInterface;
use Spryker\Zed\ProductLabel\Persistence\ProductLabelRepositoryInterface;

class LabelDeleter implements LabelDeleterInterface
{
    use TransactionTrait;

    protected const MESSAGE_PRODUCT_LABEL_NOT_FOUND = 'Missing product label';

    /**
     * @var \Spryker\Zed\ProductLabel\Persistence\ProductLabelEntityManagerInterface
     */
    private $productLabelEntityManager;

    /**
     * @var \Spryker\Zed\ProductLabel\Persistence\ProductLabelRepositoryInterface
     */
    private $productLabelRepository;

    /**
     * @param \Spryker\Zed\ProductLabel\Persistence\ProductLabelEntityManagerInterface $productLabelEntityManager
     * @param \Spryker\Zed\ProductLabel\Persistence\ProductLabelRepositoryInterface $productLabelRepository
     */
    public function __construct(
        ProductLabelEntityManagerInterface $productLabelEntityManager,
        ProductLabelRepositoryInterface $productLabelRepository
    ) {
        $this->productLabelEntityManager = $productLabelEntityManager;
        $this->productLabelRepository = $productLabelRepository;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductLabelTransfer $productLabelTransfer
     *
     * @return \Generated\Shared\Transfer\ProductLabelResponseTransfer
     */
    public function remove(ProductLabelTransfer $productLabelTransfer): ProductLabelResponseTransfer
    {
        $this->assertProductLabel($productLabelTransfer);

        $productLabelResponseTransfer = (new ProductLabelResponseTransfer())
            ->setIsSuccessful(true);

        $productLabelTransfer = $this->productLabelRepository
            ->findProductLabelByIdProductLabel($productLabelTransfer->getIdProductLabel());

        if ($productLabelTransfer === null) {
            return $productLabelResponseTransfer
                ->setIsSuccessful(false)
                ->addMessage($this->createProductLabelNotFoundMessage(static::MESSAGE_PRODUCT_LABEL_NOT_FOUND));
        }

        $this->executeProductLabelDeleteTransaction($productLabelTransfer);

        return $productLabelResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductLabelTransfer $productLabelTransfer
     *
     * @return void
     */
    protected function executeProductLabelDeleteTransaction(ProductLabelTransfer $productLabelTransfer)
    {
        $this->getTransactionHandler()->handleTransaction(function () use ($productLabelTransfer) {
            $this->productLabelEntityManager->deleteProductLabelProductAbstractRelations($productLabelTransfer->getIdProductLabel());
            $this->productLabelEntityManager->deleteProductLabelLocalizedAttributes($productLabelTransfer->getIdProductLabel());
            $this->productLabelEntityManager->deleteProductLabel($productLabelTransfer->getIdProductLabel());
        });
    }

    /**
     * @param \Generated\Shared\Transfer\ProductLabelTransfer $productLabelTransfer
     *
     * @return void
     */
    protected function assertProductLabel(ProductLabelTransfer $productLabelTransfer)
    {
        $productLabelTransfer
            ->requireIdProductLabel()
            ->requireName();
    }

    /**
     * @param string $message
     *
     * @return \Generated\Shared\Transfer\MessageTransfer
     */
    protected function createProductLabelNotFoundMessage(string $message): MessageTransfer
    {
        return (new MessageTransfer())->setValue($message);
    }
}