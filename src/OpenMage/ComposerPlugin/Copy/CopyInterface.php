<?php

/**
 * @category   OpenMage
 * @package    VendorCopy
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\ComposerPlugin\Copy;

/**
 * CopyInterface
 */
interface CopyInterface
{
    public const EXTRA_MAGENTO_ROOT_DIR = 'magento-root-dir';

    public const EXTRA_UNPKG_PACKAGES   = 'openmage-unpkg-packages';

    public const VENDOR_DIR             = 'vendor-dir';

    public function getCopyTarget(): string;
}
