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

namespace OpenMage\ComposerPlugin;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Symfony\Component\Finder\Finder;

/**
 * Class Plugin
 */
class Plugin implements PluginInterface, EventSubscriberInterface
{
    protected Composer $composer;

    protected IOInterface $io;

    /**
     * @see PluginInterface::activate
     */
    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io): void {}

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
     * @see \OpenMage\ComposerPlugin\Copy\Plugins\JQuery
     * @see \OpenMage\ComposerPlugin\Copy\Plugins\TinyMce
     * @see \OpenMage\ComposerPlugin\Copy\Plugins\TinyMceLanguages
     */
    public function processCopy(Event $event): void
    {
        $plugins = $this->getPlugins(__DIR__ . '/Copy/Plugins');
        foreach ($plugins as $plugin) {
            $pluginLoaded = new $plugin($event);
            if ($pluginLoaded instanceof Copy\CopyFromComposerInterface) {
                $pluginLoaded->processComposerInstall();
                continue;
            }
            if ($pluginLoaded instanceof Copy\CopyFromNpmInterface) {
                $pluginLoaded->processNpmInstall();
                continue;
            }
            $this->io->write('Could not load ' . $plugin);
        }
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
        $directoriesAndFilename = explode('/', $filename);
        $filename = array_pop($directoriesAndFilename);
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
