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
 * Class JQuery
 */
class ChartJs extends Copy\AbstractCopyPlugin implements Copy\CopyFromComposerInterface, Copy\CopyFromUnpkgInterface
{
    public function getUnpkgName(): string
    {
        return 'chart.js';
    }

    public function getUnpkgVersion(): string
    {
        /** @var string $version */
        $version = InstalledVersions::getPrettyVersion($this->getComposerName());
        return ltrim($version, 'v');
    }

    public function getUnpkgSource(): string
    {
        return 'dist';
    }

    public function getUnpkgFiles(): array
    {
        return [
            'chart.umd.js',
            'chart.umd.js.map',
            'helpers.js',
            'helpers.js.map',
        ];
    }

    public function getComposerName(): string
    {
        return 'nnnick/chartjs';
    }

    public function getComposerSource(): string
    {
        return 'dist';
    }

    public function getComposerFiles(): array
    {
        return ['*.js', '*.map'];
    }

    public function getCopyTarget(): string
    {
        return 'js/lib/chartjs';
    }
}
