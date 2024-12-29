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
interface PluginInterface
{
    /**
     * Composer vendor/name
     */
    public function getVendorName(): string;

    /**
     * Path to source files inside vendor directory
     */
    public function getCopySource(): string;

    /**
     * Path to copy target
     */
    public function getCopyTarget(): string;

    /**
     * Filename patternst to copy
     *
     * @return string[]
     */
    public function getFilesByName(): array;

    public function copyFiles(): void;
}
