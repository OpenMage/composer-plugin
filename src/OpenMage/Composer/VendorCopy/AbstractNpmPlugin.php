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

namespace OpenMage\Composer\VendorCopy;

use Composer\InstalledVersions;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractNpmPlugin extends AbstractPlugin implements PluginNpmInterface
{
    public function downloadNpmFiles(): void
    {
        $filesystem = new Filesystem();

        if ($filesystem->exists($this->getFullCopySource())) {
            parent::copyFiles();
            return;
        }

        $this->event->getIO()->write(sprintf(
            'No files found in %s',
            $this->getFullCopySource(),
        ));

        $this->event->getIO()->write(sprintf(
            'Try to download %s %s from %s',
            $this->getNpmPackageName(),
            $this->getPrettyVersion(),
            self::NPM_FALLBACK_URL,
        ));

        foreach ($this->getNpmPackageFiles() as $fileName) {
            $sourceFilePath = $this->getNpmFilePath() . $fileName;
            $content        = file_get_contents($sourceFilePath);

            if (!$content) {
                $this->event->getIO()->write(sprintf('Could not read from %s', $sourceFilePath));
                return;
            }

            try {
                $targetFilePath = $this->getFullCopySource() . '/' . $fileName;
                $filesystem->dumpFile($targetFilePath, $content);
                if ($this->event->getIO()->isVerbose()) {
                    $this->event->getIO()->write(sprintf('Added %s', $fileName));
                }
            } catch (IOException $IOException) {
                $this->event->getIO()->write($IOException->getMessage());
                return;
            }
        }
    }

    private function getNpmFilePath(): string
    {
        $search  = ['{{package}}', '{{version}}'];
        $replace = [$this->getNpmPackageName(), $this->getPrettyVersion()];

        return str_replace($search, $replace, self::NPM_FALLBACK_URL);
    }

    private function getPrettyVersion(): string
    {
        return ltrim((string) InstalledVersions::getPrettyVersion($this->getComposerPackageName()), 'v');
    }
}
