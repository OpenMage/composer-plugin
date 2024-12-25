<?php

declare(strict_types=1);

namespace OpenMage\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

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
        $plugin = new Plugin\JQuery($event);
        $plugin->copyFiles();

        $plugin = new Plugin\TinyMce($event);
        $plugin->copyFiles();

        $plugin = new Plugin\TinyMceLanguages($event);
        $plugin->copyFiles();
    }
}
