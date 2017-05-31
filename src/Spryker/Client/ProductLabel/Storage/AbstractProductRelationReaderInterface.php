<?php
/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */


namespace Spryker\Client\ProductLabel\Storage;


interface AbstractProductRelationReaderInterface
{

    /**
     * @param int $idProductAbstract
     * @param string $localeName
     *
     * @return \Generated\Shared\Transfer\ProductLabelStorageProjectionTransfer[]
     */
    public function getLabelsForAbstractProduct($idProductAbstract, $localeName);

}