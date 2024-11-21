<?php

declare(strict_types=1);

namespace OpenMage\Composer\Plugin;

use Composer\Composer;
use Composer\InstalledVersions;
use Composer\IO\IOInterface;
use Composer\Package\BasePackage;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Class TinyMce
 */
class TinyMce implements PluginInterface
{
    public const TINYMCE_MODULE             = 'tinymce/tinymce';
    public const TINYMCE_MODULE_LANGUAGE    = 'mklkj/tinymce-i18n';
    public const TINYMCE_LICENSE_FILE       = 'LICENSE_TINYMCE.txt';
    public const TINYMCE_LICENSE_NOTE       = 'LICENSE_TINYMCE_OPENAMGE.txt';

    public array $modules = [
        self::TINYMCE_MODULE            => 'js/tinymce',
        self::TINYMCE_MODULE_LANGUAGE   => 'js/tinymce/langs',
    ];

    public array $installedModules = [];

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

    /**
     * @param Composer $composer
     * @param IOInterface $io
     * @return void
     */
    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * @param Composer $composer
     * @param IOInterface $io
     * @return void
     */
    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * @param Event $event
     * @return void
     */
    public function process(Event $event): void
    {
        $io = $event->getIO();
        foreach ($this->modules as $vendorModule => $target) {
            $module = $this->getTinyMceModule($event, $io, $vendorModule);
            if ($module) {
                $rootDir    = getcwd();
                $vendorDir  = $event->getComposer()->getConfig()->get('vendor-dir');

                $version     = $this->getInstalledVersion(self::TINYMCE_MODULE);
                $mainVersion = $version[0] ?? null;


                $source = $vendorDir . '/' . $vendorModule;
                $target = $rootDir . '/' . $target;

                if ($vendorModule === self::TINYMCE_MODULE) {
                    $filesystem = new Filesystem();
                    switch ($mainVersion) {
                        case '6':
                            $filesystem->remove([
                                $rootDir . '/' . self::TINYMCE_LICENSE_FILE,
                                $source . '/' . self::TINYMCE_LICENSE_NOTE
                            ]);
                            break;
                        case '7':
                            $content = <<<TEXT
THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT
NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL TINYMCE OR ITS LICENSORS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN
AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR
THE USE OR OTHER DEALINGS IN THE SOFTWARE.
TEXT;
                            $filesystem->dumpFile($rootDir . '/' . self::TINYMCE_LICENSE_FILE, $content);

                            $content = <<<TEXT
THE USE OF TINYMCE IN THIS PROJECT IS POSSIBLE ON THE BASIS OF THE AGREEMENT CONCLUDED BETWEEN
THE OWNER OF TINYMCE AND THE COMMUNITY OF THIS OPEN-SOURCE PROJECT. UNDER THE CONCLUDED
AGREEMENT, IT IS PROHIBITED TO USE TINYMCE OUTSIDE OF THIS PROJECT. IF THIS IS THE CASE, TINYMCE MUST
BE USED IN LINE WITH THE ORIGINAL OPEN-SOURCE LICENSE.
TEXT;
                            $filesystem->dumpFile($source . '/' . self::TINYMCE_LICENSE_NOTE, $content);
                            break;
                    }
                }

                if ($vendorModule === self::TINYMCE_MODULE_LANGUAGE) {
                    $source = $source . '/langs' . $mainVersion;
                }

                $this->copy($source, $target);
            }
        }
    }

    private function getTinyMceModule(Event $event, IOInterface $io, string $module): ?BasePackage
    {
        if (!InstalledVersions::isInstalled($module)) {
            return null;
        }

        $locker = $event->getComposer()->getLocker();
        $repo   = $locker->getLockedRepository();

        foreach ($repo->getPackages() as $package) {
            if ($package->getName() === $module) {
                $this->setInstalledVersion($module, $package->getVersion());
                if ($io->isVerbose()) {
                    $io->write(sprintf('%s found with version %s', $module, $package->getVersion()));
                }
                return $package;
            }
        }
        return null;
    }

    private function getInstalledVersion(string $module): string
    {
        return $this->installedModules[$module];
    }

    private function setInstalledVersion(string $module, string $version): void
    {
        $this->installedModules[$module] = $version;
    }

    private function copy(string $source, string $target): void
    {
        $filesystem = new Filesystem();
        $finder = new Finder();
        $finder->in($source)->name('*.js');
        foreach ($finder as $file) {
            $filesystem->copy($file->getPathname(), $target . '/' . $file->getRelativePathname());
        }
    }
}
