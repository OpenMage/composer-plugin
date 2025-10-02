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
use ErrorException;
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
    public array $composerPackages = [];

    /**
     * Packages installad via Unpkg downloadd
     *
     * @var array<string, array<string, string>>
     */
    public array $unpkgPackages = [];

    /**
     * Composer event
     */
    protected ?Event $event;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(?Event $event)
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
        $package = $this->getComposerPackage();
        if (!$package instanceof \Composer\Package\BasePackage || !$this instanceof CopyFromComposerInterface) {
            return;
        }

        $copySourcePath = sprintf(
            '%s/%s/%s',
            $this->getVendorDirectoryFromComposer(),
            $this->getComposerName(),
            $this->getComposerSource(),
        );

        $event      = $this->getEvent();
        $filesystem = $this->getFileSystem();

        if (!$filesystem->exists($copySourcePath) && $this instanceof CopyFromUnpkgInterface) {
            if ($event instanceof Event && $event->getIO()->isVerbose()) {
                $event->getIO()->write(sprintf(
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
                if ($event instanceof Event && $event->getIO()->isVeryVerbose()) {
                    $event->getIO()->write(sprintf('Copy %s to %s', $copySource, $copytarget));
                }
            } catch (IOException $exception) {
                if ($event instanceof Event) {
                    $event->getIO()->write($exception->getMessage());
                }
            }
        }
    }

    /**
     * Copy files as defined in Unpkg copy-plugin
     */
    public function processUnpkgInstall(): void
    {
        if (!$this instanceof CopyFromUnpkgInterface || !$this->getUnpkgVersion()) {
            return;
        }

        $event      = $this->getEvent();
        $sourcePath = $this->getUnpkSourcePath();

        if ($event instanceof Event && $event->getIO()->isVerbose()) {
            $event->getIO()->write(sprintf(
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
            } catch (ErrorException $errorException) {
                if ($event instanceof Event) {
                    $event->getIO()->write($errorException->getMessage());
                }

                return;
            }

            if (!$content) {
                if ($event instanceof Event) {
                    $event->getIO()->write(sprintf('Could not read from %s', $sourceFilePath));
                }

                return;
            }

            try {
                $targetFilePath = $this->getCopyTargetPath() . '/' . $fileName;
                $this->getFileSystem()->dumpFile($targetFilePath, $content);
                if ($event instanceof Event && $event->getIO()->isVerbose()) {
                    $event->getIO()->write(sprintf('Added %s', $targetFilePath));
                }
            } catch (IOException $exception) {
                if ($event instanceof Event) {
                    $event->getIO()->write($exception->getMessage());
                }

                return;
            }
        }
    }

    public function getComposerPackage(): ?BasePackage
    {
        if (!$this instanceof CopyFromComposerInterface) {
            return null;
        }

        $vendorName = $this->getComposerName();
        $module = $this->getInstalledComposerPackage($vendorName);
        if ($module instanceof \Composer\Package\BasePackage) {
            return $module;
        }

        $event = $this->getEvent();
        if (!$event instanceof Event) {
            return null;
        }

        $locker = $event->getComposer()->getLocker();
        $lockArrayRepository   = $locker->getLockedRepository();

        foreach ($lockArrayRepository->getPackages() as $basePackage) {
            if ($basePackage->getName() === $vendorName) {
                $this->setInstalledComposerPackage($vendorName, $basePackage);
                if ($event->getIO()->isVerbose()) {
                    $event->getIO()->write(sprintf('%s found with version %s', $vendorName, $basePackage->getVersion()));
                }

                return $this->getInstalledComposerPackage($vendorName);
            }
        }

        return null;
    }

    /**
     * Get path to Unpkg dist
     */
    protected function getUnpkSourcePath(): string
    {
        if ($this instanceof CopyFromUnpkgInterface) {
            $search  = ['{{package}}', '{{version}}'];
            $replace = [$this->getUnpkgName(), $this->getUnpkgVersion()];
            $path    = str_replace($search, $replace, CopyFromUnpkgInterface::UNPKG_URL);
            return $path . ($this->getUnpkgSource() !== '' && $this->getUnpkgSource() !== '0' ? $this->getUnpkgSource() . '/' : '');
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
        $event = $this->getEvent();
        if (!$event instanceof Event) {
            return '';
        }

        /** @var string $vendorDir */
        $vendorDir = $event->getComposer()->getConfig()->get(self::VENDOR_DIR);
        return $vendorDir;
    }

    /**
     * Get openmage composer install directory
     */
    protected function getMageRootDirectoryFromComposer(): string
    {
        $event = $this->getEvent();
        if (!$event instanceof Event) {
            return '';
        }

        $composerExtra  = $event->getComposer()->getPackage()->getExtra();
        if (array_key_exists(self::EXTRA_MAGENTO_ROOT_DIR, $composerExtra) &&
            $composerExtra[self::EXTRA_MAGENTO_ROOT_DIR] !== '.') {
            return $composerExtra[self::EXTRA_MAGENTO_ROOT_DIR] . '/';
        }

        return '';
    }

    protected function getInstalledComposerPackage(string $vendorName): ?BasePackage
    {
        return $this->composerPackages[$vendorName] ?? null;
    }

    public function getFileSystem(): Filesystem
    {
        return new Filesystem();
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }
}
