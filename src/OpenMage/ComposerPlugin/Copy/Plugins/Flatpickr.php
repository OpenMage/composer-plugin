<?php

/**
 * @category   OpenMage
 * @package    VendorCopy
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\ComposerPlugin\Copy\Plugins;

use OpenMage\ComposerPlugin\Copy;

/**
 * Class Flatpickr
 */
class Flatpickr extends Copy\AbstractCopyPlugin implements Copy\CopyFromNpmInterface
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

    public function getCopyTarget(): string
    {
        return 'js/lib/flatpickr';
    }
}
