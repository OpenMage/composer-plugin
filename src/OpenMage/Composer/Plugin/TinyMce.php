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

    /**
     * @var string[]
     */
    public static array $modules = [
        self::TINYMCE_MODULE            => 'js/tinymce',
        self::TINYMCE_MODULE_LANGUAGE   => 'js/tinymce/langs',
    ];

    /**
     * @var string[]
     */
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

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }

    public function process(Event $event, string $magentoRootDir): void
    {
        $io = $event->getIO();
        foreach (self::$modules as $moduleName => $copyTarget) {
            $tinyMceModule = $this->getTinyMceModule($event, $io, $moduleName);
            if ($tinyMceModule) {
                $rootDir    = getcwd();
                /** @var string $vendorDir */
                $vendorDir   = $event->getComposer()->getConfig()->get('vendor-dir');

                $version     = $this->getInstalledVersion(self::TINYMCE_MODULE);
                $mainVersion = $version[0] ?? null;

                $copySource  = $vendorDir . '/' . $moduleName;
                $copyTarget  = $rootDir . '/' . $copyTarget;

                if ($moduleName === self::TINYMCE_MODULE) {
                    switch ((int) $mainVersion) {
                        case 6:
                            $files = [
                                $rootDir . '/' . self::TINYMCE_LICENSE_FILE,
                                $copySource . '/' . self::TINYMCE_LICENSE_NOTE
                            ];
                            $this->removedTinyMceLicenseFiles($files);
                            break;
                        case 7:
                            $this->addTinyMceLicenseFile($rootDir);
                            $this->addTinyMceLicenseNote($copySource);
                            break;
                    }
                }

                if ($moduleName === self::TINYMCE_MODULE_LANGUAGE) {
                    $copySource = $copySource . '/langs' . $mainVersion;
                }

                $this->copy($copySource, $magentoRootDir . $copyTarget);
            }
        }
    }

    private function getInstalledVersion(string $module): string
    {
        return $this->installedModules[$module];
    }

    private function setInstalledVersion(string $module, string $version): void
    {
        $this->installedModules[$module] = $version;
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

    private function copy(string $source, string $target): void
    {
        $filesystem = new Filesystem();
        $finder = new Finder();
        $finder->in($source)->name('*.js');
        foreach ($finder as $file) {
            $copySource = $file->getPathname();
            $copytarget = $target . '/' . $file->getRelativePathname();
            $filesystem->copy($copySource, $copytarget);
            if ($io->isVeryVerbose()) {
                $io->write(sprintf('Copy %s to %s', $copySource, $copytarget));
            }
        }
    }

    private function addTinyMceLicenseFile(string $targetDir): void
    {
        $content = <<<TEXT
THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT
NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL TINYMCE OR ITS LICENSORS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN
AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR
THE USE OR OTHER DEALINGS IN THE SOFTWARE.
TEXT;

        $filesystem = new Filesystem();
        $filesystem->dumpFile($targetDir . '/' . self::TINYMCE_LICENSE_FILE, $content);
    }

    private function addTinyMceLicenseNote(string $targetDir): void
    {
        $content = <<<TEXT
THE USE OF TINYMCE IN THIS PROJECT IS POSSIBLE ON THE BASIS OF THE AGREEMENT CONCLUDED BETWEEN
THE OWNER OF TINYMCE AND THE COMMUNITY OF THIS OPEN-SOURCE PROJECT. UNDER THE CONCLUDED
AGREEMENT, IT IS PROHIBITED TO USE TINYMCE OUTSIDE OF THIS PROJECT. IF THIS IS THE CASE, TINYMCE MUST
BE USED IN LINE WITH THE ORIGINAL OPEN-SOURCE LICENSE.
TEXT;

        $filesystem = new Filesystem();
        $filesystem->dumpFile($targetDir . '/' . self::TINYMCE_LICENSE_NOTE, $content);
    }

    private function removedTinyMceLicenseFiles(array $files): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove($files);
    }
}
