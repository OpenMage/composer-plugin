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

namespace OpenMage\ComposerPlugin\Copy\Npm;

/**
 * PluginInterface
 */
interface PluginInterface
{
    public const NPM_FALLBACK_URL = 'https://unpkg.com/{{package}}@{{version}}/dist/';

    /**
     * Npm name
     */
    public function getNpmPackageName(): string;

    /**
     * @return string[]
     */
    public function getNpmPackageFiles(): array;

    public function processNpmInstall(): void;
}
