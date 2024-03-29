<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductLabel\Plugin;

use Generated\Shared\Search\PageIndexMap;
use Generated\Shared\Transfer\FacetConfigTransfer;
use Spryker\Client\Catalog\Dependency\Plugin\FacetConfigTransferBuilderPluginInterface;
use Spryker\Client\Kernel\AbstractPlugin;
use Spryker\Shared\Search\SearchConfig;

class ProductLabelFacetConfigTransferBuilderPlugin extends AbstractPlugin implements FacetConfigTransferBuilderPluginInterface
{
    /**
     * @var string
     */
    public const NAME = 'label';

    /**
     * @var string
     */
    public const PARAMETER_NAME = 'label';

    /**
     * @return \Generated\Shared\Transfer\FacetConfigTransfer
     */
    public function build()
    {
        return (new FacetConfigTransfer())
            ->setName(static::NAME)
            ->setParameterName(static::PARAMETER_NAME)
            ->setFieldName(PageIndexMap::STRING_FACET)
            ->setType(SearchConfig::FACET_TYPE_ENUMERATION)
            ->setIsMultiValued(true)
            ->setValueTransformer(ProductLabelFacetValueTransformerPlugin::class);
    }
}
