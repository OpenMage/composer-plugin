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

namespace OpenMage\ComposerPlugin\Copy\Composer\Plugins;

use OpenMage\ComposerPlugin\Copy;

/**
 * Class JQuery
 */
class ChartJs extends Copy\AbstractCopyPlugin implements Copy\Composer\PluginInterface, Copy\Npm\PluginInterface
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
        return 'js/chartjs';
    }

    public function getFilesByName(): array
    {
        return ['*.js', '*.map'];
    }
}
