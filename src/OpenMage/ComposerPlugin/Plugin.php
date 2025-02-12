<?php

/**
 * @category   OpenMage
 * @package    VendorCopy
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\ComposerPlugin;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use OpenMage\ComposerPlugin\Copy\CopyInterface;
use OpenMage\ComposerPlugin\Copy\Unpkg\Config;
use OpenMage\ComposerPlugin\Copy\Unpkg\Generic;
use Symfony\Component\Finder\Finder;

/**
 * Class Plugin
 */
class Plugin implements PluginInterface, EventSubscriberInterface
{
    protected ?Composer $composer = null;

    protected ?IOInterface $io = null;

    /**
     * @codeCoverageIgnore
     * @see PluginInterface::activate
     */
    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function deactivate(Composer $composer, IOInterface $io): void {}

    /**
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function uninstall(Composer $composer, IOInterface $io): void {}

    /**
     * @see EventSubscriberInterface::getSubscribedEvents
     * @see processCopy
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => [
                ['processCopy'],
            ],
            ScriptEvents::POST_UPDATE_CMD  => [
                ['processCopy'],
            ],
        ];
    }

    /**
     * @see \OpenMage\ComposerPlugin\Copy\Plugins\ChartJs
     * @see \OpenMage\ComposerPlugin\Copy\Plugins\Flatpickr
     * @see \OpenMage\ComposerPlugin\Copy\Plugins\FlowJs
     * @see \OpenMage\ComposerPlugin\Copy\Plugins\JQuery
     * @see \OpenMage\ComposerPlugin\Copy\Plugins\TinyMce
     * @see \OpenMage\ComposerPlugin\Copy\Plugins\TinyMceLanguages
     */
    public function processCopy(?Event $event): void
    {
        $plugins = $this->getPlugins(__DIR__ . '/Copy/Plugins');
        foreach ($plugins as $plugin) {
            $pluginLoaded = new $plugin($event);
            if ($pluginLoaded instanceof Copy\AbstractCopyPlugin &&
                $pluginLoaded instanceof Copy\CopyFromComposerInterface
            ) {
                $pluginLoaded->processComposerInstall();
                continue;
            }
            if ($pluginLoaded instanceof Copy\AbstractCopyPlugin &&
                $pluginLoaded instanceof Copy\CopyFromUnpkgInterface
            ) {
                $pluginLoaded->processUnpkgInstall();
                continue;
            }

            if ($this->io) {
                $this->io->write('Could not load ' . $plugin);
            }
        }

        $plugins = $this->getUnpkgPackagesFromConfig();
        foreach ($plugins as $pluginConfig) {
            $pluginLoaded = new Generic($event, $pluginConfig);
            $pluginLoaded->processUnpkgInstall();
        }
    }

    /**
     * @return Config[]
     */
    private function getUnpkgPackagesFromConfig(): array
    {
        $packages = [];

        if (is_null($this->composer)) {
            return $packages;
        }

        $extra = $this->composer->getPackage()->getExtra();

        if (!isset($extra[CopyInterface::EXTRA_UNPKG_PACKAGES])) {
            return $packages;
        }

        $config = $extra[CopyInterface::EXTRA_UNPKG_PACKAGES];

        if (!is_array($config)) {
            if ($this->io) {
                $this->io->write(sprintf('Configuration is invalid for %s', CopyInterface::EXTRA_UNPKG_PACKAGES));
            }
            return $packages;
        }

        foreach ($config as $packageName => $packageConfig) {
            $config = new Config();
            $packageConfig = $config->getValidatedConfig($packageConfig);
            if (!$packageConfig) {
                if ($this->io) {
                    $this->io->write(sprintf('Configuration is invalid for %s', $packageName));
                }
                continue;
            }

            $config
                ->setUnpkgName($packageName)
                ->setUnpkgVersion($packageConfig['version'])
                ->setUnpkgSource($packageConfig['source'])
                ->setUnpkgFiles($packageConfig['files'])
                ->setCopyTarget($packageConfig['target']);

            $packages[] = $config;
        }
        return $packages;
    }

    /**
     * @return string[]
     */
    private function getPlugins(string $path): array
    {
        $filenames  = $this->getFilenames($path);
        $namespaces = [];
        foreach ($filenames as $filename) {
            $namespaces[] = $this->getFullNamespace($filename) . '\\' . $this->getClassName($filename);
        }
        return $namespaces;
    }

    private function getClassName(string $filename): string
    {
        $fullFilename = explode('/', $filename);
        $filename = array_pop($fullFilename);
        $nameAndExtension = explode('.', $filename);
        return array_shift($nameAndExtension);
    }

    private function getFullNamespace(string $filename): string
    {
        $lines = (array) file($filename);
        $array = (array) preg_grep('/^namespace /', $lines);
        $namespaceLine = array_shift($array);
        $match = [];
        preg_match('/^namespace (.*);$/', $namespaceLine, $match);
        return (string) array_pop($match);
    }

    /**
     * @return string[]
     */
    private function getFilenames(string $path): array
    {
        $finderFiles = Finder::create()
            ->files()
            ->in($path)
            ->name('*.php');

        $filenames = [];
        foreach ($finderFiles as $finderFile) {
            $filenames[] = $finderFile->getRealPath();
        }
        return $filenames;
    }
}
