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

namespace OpenMage\Composer\VendorCopy\Plugins;

use OpenMage\Composer\VendorCopy\AbstractNpmPlugin;

/**
 * Class Flatpickr
 */
class Flatpickr extends AbstractNpmPlugin
{
    public function getNpmPackageName(): string
    {
        return 'flatpickr';
    }

    public function getNpmPackageFiles(): array
    {
        return [
            'flatpickr.min.css',
            'flatpickr.min.js',
        ];
    }

    public function getComposerPackageName(): string
    {
        return 'flatpickr/flatpickr';
    }

    public function getCopySource(): string
    {
        return 'dist';
    }

    public function getCopyTarget(): string
    {
        return 'js/flatpickr';
    }

    public function getFilesByName(): array
    {
        return ['*.css', '*.js'];
    }
}
