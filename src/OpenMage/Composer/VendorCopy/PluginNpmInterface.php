<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    VendorCopy
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Composer\VendorCopy;

/**
 * VendorCopyInterface interface
 */
interface PluginNpmInterface
{
    public const NPM_FALLBACK_URL = 'https://unpkg.com/browse/{{flatpickr}}@{{version}}/dist/';

    /**
     * NPM package name
     */
    public function getNpmPackageName(): string;

    public function getNpmPackageFiles(): array;

    public function downloadNpmFiles(): void;
}
