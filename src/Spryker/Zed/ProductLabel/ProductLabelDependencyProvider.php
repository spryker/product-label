<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductLabel;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\ProductLabel\Dependency\Facade\ProductLabelToEventBridge;
use Spryker\Zed\ProductLabel\Dependency\Facade\ProductLabelToProductBridge;
use Spryker\Zed\ProductLabel\Dependency\Facade\ProductLabelToTouchBridge;

/**
 * @method \Spryker\Zed\ProductLabel\ProductLabelConfig getConfig()
 */
class ProductLabelDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const FACADE_TOUCH = 'FACADE_TOUCH';

    /**
     * @var string
     */
    public const FACADE_PRODUCT = 'FACADE_PRODUCT';

    /**
     * @var string
     */
    public const FACADE_EVENT = 'FACADE_EVENT';

    /**
     * @var string
     */
    public const PLUGIN_PRODUCT_LABEL_RELATION_UPDATERS = 'PLUGIN_PRODUCT_LABEL_RELATION_UPDATERS';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = $this->addTouchFacade($container);
        $container = $this->addProductFacade($container);
        $container = $this->addEventFacade($container);

        $container = $this->addProductLabelRelationUpdaterPlugins($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addTouchFacade(Container $container)
    {
        $container->set(static::FACADE_TOUCH, function (Container $container) {
            return new ProductLabelToTouchBridge($container->getLocator()->touch()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductFacade(Container $container)
    {
        $container->set(static::FACADE_PRODUCT, function (Container $container) {
            return new ProductLabelToProductBridge($container->getLocator()->product()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addEventFacade(Container $container)
    {
        $container->set(static::FACADE_EVENT, function (Container $container) {
            return new ProductLabelToEventBridge($container->getLocator()->event()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductLabelRelationUpdaterPlugins(Container $container)
    {
        $container->set(static::PLUGIN_PRODUCT_LABEL_RELATION_UPDATERS, function (Container $container) {
            return $this->getProductLabelRelationUpdaterPlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Zed\ProductLabel\Dependency\Plugin\ProductLabelRelationUpdaterPluginInterface>
     */
    protected function getProductLabelRelationUpdaterPlugins()
    {
        return [];
    }
}
