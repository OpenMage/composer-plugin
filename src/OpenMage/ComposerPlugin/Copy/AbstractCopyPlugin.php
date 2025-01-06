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

namespace OpenMage\ComposerPlugin\Copy;

use Composer\Package\BasePackage;
use Composer\Script\Event;
use Exception;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * AbstractCopyPlugin
 */
abstract class AbstractCopyPlugin implements CopyInterface
{
    /**
     * Packages installed via composer
     *
     * @var BasePackage[]
     */
    public array $installedComposerPackages = [];

    /**
     * Packages installad via NPM downloadd
     *
     * @var array<string, array<string, string>>
     */
    public array $installedNpmPackages = [];

    /**
     * Package version
     */
    public ?string $version = null;

    /**
     * Composer event
     */
    protected Event $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Copy files as defined in composer copy-plugin
     *
     * Fallback to NPM download if configured
     */
    public function processComposerInstall(): void
    {
        $copySourcePath = null;
        if ($this instanceof CopyFromComposerInterface) {
            $package = $this->getComposerPackage();
            if (!$package) {
                return;
            }
            $copySourcePath = $this->getCopySourcePath();
        }

        $filesystem  = new Filesystem();

        if ($this instanceof CopyFromNpmInterface && (!$copySourcePath || !$filesystem->exists($copySourcePath))) {
            if ($this->event->getIO()->isVerbose()) {
                $this->event->getIO()->write(sprintf('Fallback to NPM for %s', $this->getNpmPackageName()));
            }
            $this->processNpmInstall();
            return;
        }

        if ($copySourcePath && $this instanceof CopyFromComposerInterface) {
            $finder = Finder::create()
                ->files()
                ->in($copySourcePath)
                ->name($this->getFilesByName());

            foreach ($finder as $file) {
                $copySource = $file->getPathname();
                $copytarget = $this->getCopyTargetPath() . '/' . $file->getRelativePathname();

                try {
                    $filesystem->copy($copySource, $copytarget);
                    if ($this->event->getIO()->isVeryVerbose()) {
                        $this->event->getIO()->write(sprintf('Copy %s to %s', $copySource, $copytarget));
                    }
                } catch (IOException $IOException) {
                    $this->event->getIO()->write($IOException->getMessage());
                }
            }
        }
    }

    /**
     * Copy files as defined in NPM copy-plugin
     */
    public function processNpmInstall(): void
    {
        if ($this instanceof CopyFromNpmInterface) {
            if (!$this->getVersion()) {
                return;
            }

            $sourcePath = $this->getNpmFilePath();

            if ($this->event->getIO()->isVerbose()) {
                $this->event->getIO()->write(sprintf(
                    'Trying to download %s %s from %s',
                    $this->getNpmPackageName(),
                    $this->getVersion(),
                    $sourcePath,
                ));
            }

            foreach ($this->getNpmPackageFiles() as $fileName) {
                $sourceFilePath = $sourcePath . $fileName;
                try {
                    $content = file_get_contents($sourceFilePath);
                } catch (\ErrorException $errorException) {
                    $this->event->getIO()->write($errorException->getMessage());
                    return;
                }

                if (!$content) {
                    $this->event->getIO()->write(sprintf('Could not read from %s', $sourceFilePath));
                    return;
                }

                try {
                    $filesystem = new Filesystem();
                    $targetFilePath = $this->getCopyTargetPath() . '/' . $fileName;
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
    }

    public function getComposerPackage(): ?BasePackage
    {
        if ($this instanceof CopyFromComposerInterface) {
            $vendorName = $this->getComposerPackageName();
            $module = $this->getInstalledComposerPackage($vendorName);
            if ($module) {
                return $module;
            }

            $locker = $this->event->getComposer()->getLocker();
            $repo   = $locker->getLockedRepository();

            foreach ($repo->getPackages() as $package) {
                if ($package->getName() === $vendorName) {
                    $this->setInstalledComposerPackage($vendorName, $package);
                    if ($this->event->getIO()->isVerbose()) {
                        $this->event->getIO()->write(sprintf('%s found with version %s', $vendorName, $package->getVersion()));
                    }
                    return $this->getInstalledComposerPackage($vendorName);
                }
            }
        }
        return null;
    }

    /**
     * @return array<string, string>|null
     */
    public function getNpmPackage(): ?array
    {
        if ($this instanceof CopyFromNpmInterface) {
            $vendorName = $this->getNpmPackageName();

            $locker = $this->event->getComposer()->getLocker();
            $repo   = $locker->getLockedRepository();

            $packages   = $repo->getPackages();
            $packages[] = $this->event->getComposer()->getPackage();

            foreach ($packages as $package) {
                /** @var array<string, string|array<string>> $extra */
                $extra = $package->getExtra();

                if (!isset($extra[self::EXTRA_NPM_PACKAGES][$vendorName])) {
                    continue;
                }

                $packageData = $extra[self::EXTRA_NPM_PACKAGES][$vendorName];

                if (!is_array($packageData)) {
                    throw new Exception(sprintf('Configuration is invalid for %s', $vendorName));
                }

                if (array_key_exists('version', $packageData) && is_string($packageData['version'])) {
                    $this->setInstalledNpmPackage($vendorName, $packageData);
                    if ($this->event->getIO()->isVerbose()) {
                        $this->event->getIO()->write(sprintf(
                            '%s found with version %s',
                            $vendorName,
                            $packageData['version'],
                        ));
                    }
                    return $this->getInstalledNpmPackage($vendorName);
                }
            }
        }
        return null;
    }

    /**
     * Get path to NPM dist
     */
    protected function getNpmFilePath(): string
    {
        if ($this instanceof CopyFromNpmInterface) {
            $search  = ['{{package}}', '{{version}}'];
            $replace = [$this->getNpmPackageName(), $this->getVersion()];
            return str_replace($search, $replace, CopyFromNpmInterface::NPM_FALLBACK_URL);
        }
        return '';
    }

    /**
     * Get package version
     */
    private function getVersion(): string
    {
        if (is_null($this->version)) {
            $version = '';
            switch (true) {
                case $this instanceof CopyFromComposerInterface:
                    $package = $this->getComposerPackage();
                    $version = $package ? $package->getPrettyVersion() : '';
                    break;
                case $this instanceof CopyFromNpmInterface:
                    $package = $this->getNpmPackage();
                    $version = $package ? $package['version'] : '';
                    break;
            }

            $this->version = ltrim($version, 'v');
        }

        return $this->version;
    }

    /**
     * Get current working directory
     */
    protected function getCwd(): string
    {
        $cwd = getcwd();
        if ($cwd === false) {
            throw new Exception('This should not happen.');
        }
        return $cwd;
    }

    /**
     * Get composer vendor directory
     */
    protected function getVendorDirectoryFromComposer(): string
    {
        /** @var string $vendorDir */
        $vendorDir = $this->event->getComposer()->getConfig()->get(self::VENDOR_DIR);
        return $vendorDir;
    }

    /**
     * Get openmage composer install directory
     */
    protected function getMageRootDirectoryFromComposer(): string
    {
        $composerExtra  = $this->event->getComposer()->getPackage()->getExtra();
        $magentoRootDir = '';

        if (array_key_exists(self::EXTRA_MAGENTO_ROOT_DIR, $composerExtra) &&
            $composerExtra[self::EXTRA_MAGENTO_ROOT_DIR] !== '.'
        ) {
            $magentoRootDir = $composerExtra[self::EXTRA_MAGENTO_ROOT_DIR] . '/';
        }
        return $magentoRootDir;
    }

    protected function getCopySourcePath(): string
    {
        if ($this instanceof CopyFromComposerInterface) {
            return sprintf(
                '%s/%s/%s',
                $this->getVendorDirectoryFromComposer(),
                $this->getComposerPackageName(),
                $this->getCopySource(),
            );
        }
        return '';
    }

    protected function getCopyTargetPath(): string
    {
        return sprintf(
            '%s/%s%s',
            $this->getCwd(),
            $this->getMageRootDirectoryFromComposer(),
            $this->getCopyTarget(),
        );
    }

    protected function getInstalledComposerPackage(string $vendorName): ?BasePackage
    {
        return $this->installedComposerPackages[$vendorName] ?? null;
    }

    private function setInstalledComposerPackage(string $vendorName, BasePackage $package): void
    {
        $this->installedComposerPackages[$vendorName] = $package;
    }

    /**
     * @return array<string, string>|null
     */
    protected function getInstalledNpmPackage(string $vendorName): ?array
    {
        return $this->installedNpmPackages[$vendorName] ?? null;
    }

    /**
     * @param array<string, string> $package
     */
    private function setInstalledNpmPackage(string $vendorName, array $package): void
    {
        $this->installedNpmPackages[$vendorName] = $package;
    }
}
