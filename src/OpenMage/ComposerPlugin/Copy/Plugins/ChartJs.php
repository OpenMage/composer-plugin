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
 * Class JQuery
 */
class ChartJs extends Copy\AbstractCopyPlugin implements Copy\CopyFromComposerInterface, Copy\CopyFromNpmInterface
{
    public function getNpmPackageName(): string
    {
        return 'chart.js';
    }

    public function getNpmPackageFiles(): array
    {
        return [
            'chart.umd.js',
            'chart.umd.js.map',
            'helpers.js',
            'helpers.js.map',
        ];
    }

    public function getComposerPackageName(): string
    {
        return 'nnnick/chartjs';
    }

    public function getCopySource(): string
    {
        return 'dist';
    }

    public function getCopyTarget(): string
    {
        return 'js/lib/chartjs';
    }

    public function getFilesByName(): array
    {
        return ['*.js', '*.map'];
    }
}
