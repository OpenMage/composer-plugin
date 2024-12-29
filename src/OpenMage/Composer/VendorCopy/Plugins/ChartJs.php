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

use Composer\InstalledVersions;
use OpenMage\Composer\VendorCopy\AbstractPlugin;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class JQuery
 */
class ChartJs extends AbstractPlugin
{
    public const MIN_VERSION        = '2.9.4';
    public const MIN_NPM_VERSION    = '4.1.0';

    public const NPM_FALLBACK_URL   = 'https://cdn.jsdelivr.net/npm/chart.js@{{version}}/dist/chart.umd.js';

    public function getVendorName(): string
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
        return ['*.css', '*.js'];
    }

    public function copyFiles(): void
    {
        $vendorName = $this->getVendorName();

        $version = ltrim((string) InstalledVersions::getPrettyVersion($vendorName), 'v');

        if (version_compare($version, self::MIN_VERSION, '>') &&
            version_compare($version, self::MIN_NPM_VERSION, '<')
        ) {
            $message = sprintf('Chart.js %s is not supported.', $version);
            $this->event->getIO()->write($message);
            return;
        }

        $fileName   = 'Chart.min.js';
        $filePath   = $this->getVendorDirectory() . '/' . $vendorName . '/dist/' . $fileName;

        if (version_compare($version, self::MIN_NPM_VERSION, '>=')) {
            $distUrl = str_replace('{{version}}', $version, self::NPM_FALLBACK_URL);
            $content = file_get_contents($distUrl);

            $message = sprintf('Try to download Chart.js %s from %s', $version, $distUrl);
            $this->event->getIO()->write($message);

            if (!$content) {
                $this->event->getIO()->write(sprintf('Could not read from %s', $distUrl));
                return;
            }

            $filesystem = new Filesystem();

            try {
                $filesystem->dumpFile($filePath, $content);
                if ($this->event->getIO()->isVerbose()) {
                    $this->event->getIO()->write(sprintf('Added %s', $fileName));
                }
            } catch (IOException $IOException) {
                $this->event->getIO()->write($IOException->getMessage());
                return;
            }
        }

        parent::copyFiles();
    }
}
