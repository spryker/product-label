<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductLabel\Persistence\Mapper;

use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\SpyLocaleEntityTransfer;
use Orm\Zed\Locale\Persistence\SpyLocale;

class LocaleMapper
{
    public function mapLocaleEntityToLocaleTransfer(SpyLocale $localeEntity, LocaleTransfer $localeTransfer): LocaleTransfer
    {
        return $localeTransfer->fromArray($localeEntity->toArray(), true);
    }

    public function mapLocaleEntityTransaferToLocaleTransfer(SpyLocaleEntityTransfer $localeEntityTransfer, LocaleTransfer $localeTransfer): LocaleTransfer
    {
        return $localeTransfer->fromArray($localeEntityTransfer->toArray(), true);
    }
}
