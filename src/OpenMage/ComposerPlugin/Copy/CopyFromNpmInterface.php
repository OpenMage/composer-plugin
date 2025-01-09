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
 * PluginInterface
 */
interface CopyFromNpmInterface
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
