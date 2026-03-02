<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductLabel\Persistence;

use Orm\Zed\ProductLabel\Persistence\SpyProductLabelLocalizedAttributesQuery;
use Orm\Zed\ProductLabel\Persistence\SpyProductLabelProductAbstractQuery;
use Orm\Zed\ProductLabel\Persistence\SpyProductLabelQuery;
use Orm\Zed\ProductLabel\Persistence\SpyProductLabelStoreQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;
use Spryker\Zed\ProductLabel\Dependency\Facade\ProductLabelToLocaleFacadeInterface;
use Spryker\Zed\ProductLabel\Persistence\Mapper\LocaleMapper;
use Spryker\Zed\ProductLabel\Persistence\Mapper\ProductLabelLocalizedAttributesMapper;
use Spryker\Zed\ProductLabel\Persistence\Mapper\ProductLabelMapper;
use Spryker\Zed\ProductLabel\Persistence\Mapper\ProductLabelStoreRelationMapper;
use Spryker\Zed\ProductLabel\ProductLabelDependencyProvider;

/**
 * @method \Spryker\Zed\ProductLabel\ProductLabelConfig getConfig()
 * @method \Spryker\Zed\ProductLabel\Persistence\ProductLabelQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\ProductLabel\Persistence\ProductLabelEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\ProductLabel\Persistence\ProductLabelRepositoryInterface getRepository()
 */
class ProductLabelPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\ProductLabel\Persistence\SpyProductLabelQuery
     */
    public function createProductLabelQuery()
    {
        return SpyProductLabelQuery::create();
    }

    /**
     * @return \Orm\Zed\ProductLabel\Persistence\SpyProductLabelLocalizedAttributesQuery
     */
    public function createLocalizedAttributesQuery()
    {
        return SpyProductLabelLocalizedAttributesQuery::create();
    }

    /**
     * @return \Orm\Zed\ProductLabel\Persistence\SpyProductLabelProductAbstractQuery
     */
    public function createProductRelationQuery()
    {
        return SpyProductLabelProductAbstractQuery::create();
    }

    public function createProductLabelMapper(): ProductLabelMapper
    {
        return new ProductLabelMapper(
            $this->createProductLabelStoreRelationMapper(),
            $this->createProductLabelLocalizedAttributesMapper(),
        );
    }

    public function createProductLabelStoreRelationMapper(): ProductLabelStoreRelationMapper
    {
        return new ProductLabelStoreRelationMapper();
    }

    public function createProductLabelStoreQuery(): SpyProductLabelStoreQuery
    {
        return SpyProductLabelStoreQuery::create();
    }

    public function createProductLabelLocalizedAttributesMapper(): ProductLabelLocalizedAttributesMapper
    {
        return new ProductLabelLocalizedAttributesMapper($this->createLocaleMapper());
    }

    public function createLocaleMapper(): LocaleMapper
    {
        return new LocaleMapper();
    }

    public function getLocaleFacade(): ProductLabelToLocaleFacadeInterface
    {
        return $this->getProvidedDependency(ProductLabelDependencyProvider::FACADE_LOCALE);
    }
}
