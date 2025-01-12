<?php

/**
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
     * Packages installad via Unpkg downloadd
     *
     * @var array<string, array<string, string>>
     */
    public array $installedUnpkgPackages = [];

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
        if (!$this instanceof CopyFromComposerInterface) {
            return;
        }

        $package = $this->getComposerPackage();
        if (!$package) {
            return;
        }

        $copySourcePath = sprintf(
            '%s/%s/%s',
            $this->getVendorDirectoryFromComposer(),
            $this->getComposerName(),
            $this->getComposerSource(),
        );

        $filesystem  = new Filesystem();

        if (!$filesystem->exists($copySourcePath) && $this instanceof CopyFromUnpkgInterface) {
            if ($this->event->getIO()->isVerbose()) {
                $this->event->getIO()->write(sprintf(
                    'Fallback to Unpkg %s for %s',
                    $this->getUnpkgName(),
                    $this->getComposerName(),
                ));
            }
            $this->processUnpkgInstall();
            return;
        }

        $finder = Finder::create()
            ->files()
            ->in($copySourcePath)
            ->name($this->getComposerFiles());

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

    /**
     * Copy files as defined in Unpkg copy-plugin
     */
    public function processUnpkgInstall(): void
    {
        if (!$this instanceof CopyFromUnpkgInterface) {
            return;
        }

        if (!$this->getUnpkgVersion()) {
            return;
        }

        $sourcePath = $this->getUnpkSourcePath();

        if ($this->event->getIO()->isVerbose()) {
            $this->event->getIO()->write(sprintf(
                'Trying to download %s %s from %s',
                $this->getUnpkgName(),
                $this->getUnpkgVersion(),
                $sourcePath,
            ));
        }

        foreach ($this->getUnpkgFiles() as $fileName) {
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

    public function getComposerPackage(): ?BasePackage
    {
        if ($this instanceof CopyFromComposerInterface) {
            $vendorName = $this->getComposerName();
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
     * Get path to NPM dist
     */
    protected function getUnpkSourcePath(): string
    {
        if ($this instanceof CopyFromUnpkgInterface) {
            $search  = ['{{package}}', '{{version}}'];
            $replace = [$this->getUnpkgName(), $this->getUnpkgVersion()];
            $path    = str_replace($search, $replace, CopyFromUnpkgInterface::UNPKG_URL);
            return $path . $this->getUnpkgSource() . '/';
        }
        return '';
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

    private function getCopyTargetPath(): string
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
}
