<?php

declare(strict_types=1);

namespace OpenMage\Composer\VendorCopy;

use Composer\InstalledVersions;
use Composer\Package\BasePackage;
use Composer\Script\Event;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

abstract class AbstractPlugin implements PluginInterface
{
    public const EXTRA_MAGENTO_ROOT_DIR = 'magento-root-dir';
    public const VENDOR_DIR             = 'vendor-dir';

    protected Event $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @var BasePackage[]
     */
    public array $installedModules = [];

    public function getPackage(): ?BasePackage
    {
        $vendorName = $this->getVendorName();

        if (!InstalledVersions::isInstalled($vendorName)) {
            return null;
        }

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
        return getcwd();
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

        $filesystem = new Filesystem();

        $finder = new Finder();
        $finder
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

    private function getFullCopySource(): string
    {
        return sprintf(
            '%s/%s/%s',
            $this->getVendorDirectory(),
            $this->getVendorName(),
            $this->getCopySource(),
        );
    }

    private function getFullCopyTarget(): string
    {
        return sprintf(
            '%s/%s%s',
            $this->getRootDirectory(),
            $this->getOpenMageRootDirectory(),
            $this->getCopyTarget(),
        );
    }
}
