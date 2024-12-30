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
use Composer\Package\BasePackage;
use Composer\Script\Event;
use Exception;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

abstract class AbstractPlugin implements PluginInterface
{
    public const EXTRA_MAGENTO_ROOT_DIR = 'magento-root-dir';
    public const VENDOR_DIR             = 'vendor-dir';

    protected Event $event;

    private bool $isInstalled;

    private ?string $version;

    public function __construct(Event $event)
    {
        $composerPackageName = $this->getComposerPackageName();

        $this->event        = $event;
        $this->isInstalled  = InstalledVersions::isInstalled($composerPackageName);
        $this->version      = $this->isInstalled ? InstalledVersions::getPrettyVersion($composerPackageName) : null;
    }

    protected function getIsInstalled(): bool
    {
        return $this->isInstalled;
    }

    /**
     * @var BasePackage[]
     */
    public array $installedModules = [];

    public function getPackage(): ?BasePackage
    {
        if (!$this->getIsInstalled()) {
            return null;
        }

        $vendorName = $this->getComposerPackageName();
        $module = $this->getInstalledModule($vendorName);
        if ($module) {
            return $module;
        }

        $locker = $this->event->getComposer()->getLocker();
        $repo   = $locker->getLockedRepository();

        foreach ($repo->getPackages() as $package) {
            if ($package->getName() === $vendorName) {
                $this->setInstalledModule($vendorName, $package);
                if ($this->event->getIO()->isVerbose()) {
                    $this->event->getIO()->write(sprintf('%s found with version %s', $vendorName, $package->getVersion()));
                }
                return $this->getInstalledModule($vendorName);
            }
        }
        return null;
    }

    protected function getRootDirectory(): string
    {
        $cwd = getcwd();
        if ($cwd === false) {
            throw new Exception('This should not happen.');
        }
        return $cwd;
    }

    protected function getVendorDirectory(): string
    {
        /** @var string $vendorDir */
        $vendorDir = $this->event->getComposer()->getConfig()->get(self::VENDOR_DIR);
        return $vendorDir;
    }

    protected function getOpenMageRootDirectory(): string
    {
        $composer   = $this->event->getComposer();
        $extra      = $composer->getPackage()->getExtra();

        $magentoRootDir = '';
        if (array_key_exists(self::EXTRA_MAGENTO_ROOT_DIR, $extra) && $extra[self::EXTRA_MAGENTO_ROOT_DIR] !== '.') {
            $magentoRootDir = $extra[self::EXTRA_MAGENTO_ROOT_DIR] . '/';
        }
        return $magentoRootDir;
    }

    public function copyFiles(): void
    {
        if (!$this->getPackage()) {
            return;
        }

        if ($this instanceof AbstractNpmPlugin) {
            $this->downloadNpmFiles();
        }

        $filesystem = new Filesystem();

        $finder = Finder::create()
            ->files()
            ->in($this->getFullCopySource())
            ->name($this->getFilesByName());

        foreach ($finder as $file) {
            $copySource = $file->getPathname();
            $copytarget = $this->getFullCopyTarget() . '/' . $file->getRelativePathname();

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

    protected function getInstalledModule(string $vendorName): ?BasePackage
    {
        return $this->installedModules[$vendorName] ?? null;
    }

    private function setInstalledModule(string $vendorName, BasePackage $package): void
    {
        $this->installedModules[$vendorName] = $package;
    }

    protected function getFullCopySource(): string
    {
        return sprintf(
            '%s/%s/%s',
            $this->getVendorDirectory(),
            $this->getComposerPackageName(),
            $this->getCopySource(),
        );
    }

    protected function getFullCopyTarget(): string
    {
        return sprintf(
            '%s/%s%s',
            $this->getRootDirectory(),
            $this->getOpenMageRootDirectory(),
            $this->getCopyTarget(),
        );
    }
}
