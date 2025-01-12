<?php

/**
 * @category   OpenMage
 * @package    VendorCopy
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\ComposerPlugin\Copy\Plugins;

use Composer\InstalledVersions;
use OpenMage\ComposerPlugin\Copy;

/**
 * Class Flatpickr
 */
class Flatpickr extends Copy\AbstractCopyPlugin implements Copy\CopyFromUnpkgInterface
{
    public function getUnpkgName(): string
    {
        return 'flatpickr';
    }

    public function getUnpkgVersion(): string
    {
        return '';
    }

    public function getUnpkgSource(): string
    {
        return 'dist';
    }

    public function getUnpkgFiles(): array
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
