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
interface CopyFromUnpkgInterface
{
    public const UNPKG_URL = 'https://unpkg.com/{{package}}@{{version}}/';

    /**
     * Npm name
     */
    public function getUnpkgName(): string;

    public function getUnpkgVersion(): string;

    public function getUnpkgSource(): string;

    /**
     * @return string[]
     */
    public function getUnpkgFiles(): array;
}
