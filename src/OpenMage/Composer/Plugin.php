<?php

declare(strict_types=1);

namespace OpenMage\Composer;

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
     * @see processVendorCopy
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => [['processVendorCopy']],
            ScriptEvents::POST_UPDATE_CMD  => [['processVendorCopy']],
        ];
    }

    public function processVendorCopy(Event $event): void
    {
        $plugins = $this->getPlugins(__DIR__ . '/VendorCopy/Plugins');
        foreach ($plugins as $plugin) {
            $plugin = new $plugin($event);

            if (!$plugin instanceof VendorCopy\PluginInterface) {
                $this->io->write('Could not load ' . $plugin);
            }

            $plugin->copyFiles();
        }
    }

    public function getPlugins(string $path): array
    {
        $filenames = $this->getFilenames($path);
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
        $lines = file($filename);
        $array = preg_grep('/^namespace /', $lines);
        $namespaceLine = array_shift($array);
        $match = [];
        preg_match('/^namespace (.*);$/', $namespaceLine, $match);
        return array_pop($match);
    }

    private function getFilenames(string $path): array
    {
        $finderFiles = Finder::create()->files()->in($path)->name('*.php');
        $filenames = [];
        foreach ($finderFiles as $finderFile) {
            $filenames[] = $finderFile->getRealPath();
        }
        return $filenames;
    }
}
