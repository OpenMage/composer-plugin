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

namespace OpenMage\Composer\VendorCopy\Plugins;

use Composer\Package\BasePackage;
use OpenMage\Composer\VendorCopy\AbstractPlugin;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Class TinyMce
 */
class TinyMce extends AbstractPlugin
{
    public const TINYMCE_LICENSE_FILE       = 'LICENSE_TINYMCE.txt';
    public const TINYMCE_LICENSE_NOTE       = 'LICENSE_TINYMCE_OPENMAGE.txt';

    public function getComposerPackageName(): string
    {
        return 'tinymce/tinymce';
    }

    public function getCopySource(): string
    {
        return '';
    }

    public function getCopyTarget(): string
    {
        return 'js/tinymce';
    }

    public function getFilesByName(): array
    {
        return ['*.css', '*.js'];
    }

    public function copyFiles(): void
    {
        $package = $this->getPackage();
        if (!$package instanceof BasePackage) {
            return;
        }

        $version = $package->getVersion();
        switch ((int) $version[0]) {
            case 6:
                $this->removedTinyMceLicenseFiles();
                break;
            case 7:
                $this->addTinyMceLicenseFile();
                $this->addTinyMceLicenseNote();
                break;
        }

        parent::copyFiles();
    }

    private function addTinyMceLicenseFile(): void
    {
        $content = <<<TEXT
THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT
NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL TINYMCE OR ITS LICENSORS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN
AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR
THE USE OR OTHER DEALINGS IN THE SOFTWARE.
TEXT;

        $filesystem = new Filesystem();

        try {
            $filesystem->dumpFile($this->getRootDirectory() . '/' . self::TINYMCE_LICENSE_FILE, $content);
            if ($this->event->getIO()->isVerbose()) {
                $this->event->getIO()->write(sprintf('Added %s', self::TINYMCE_LICENSE_FILE));
            }
        } catch (IOException $IOException) {
            $this->event->getIO()->write($IOException->getMessage());
        }
    }

    private function addTinyMceLicenseNote(): void
    {
        $content = <<<TEXT
THE USE OF TINYMCE IN THIS PROJECT IS POSSIBLE ON THE BASIS OF THE AGREEMENT CONCLUDED BETWEEN
THE OWNER OF TINYMCE AND THE COMMUNITY OF THIS OPEN-SOURCE PROJECT. UNDER THE CONCLUDED
AGREEMENT, IT IS PROHIBITED TO USE TINYMCE OUTSIDE OF THIS PROJECT. IF THIS IS THE CASE, TINYMCE MUST
BE USED IN LINE WITH THE ORIGINAL OPEN-SOURCE LICENSE.
TEXT;

        $filesystem = new Filesystem();

        try {
            $filesystem->dumpFile($this->getVendorDirectory() . '/' . $this->getComposerPackageName() . '/' . self::TINYMCE_LICENSE_NOTE, $content);
            if ($this->event->getIO()->isVerbose()) {
                $this->event->getIO()->write(sprintf('Added %s', self::TINYMCE_LICENSE_NOTE));
            }
        } catch (IOException $IOException) {
            $this->event->getIO()->write($IOException->getMessage());
        }
    }

    private function removedTinyMceLicenseFiles(): void
    {
        $files = [
            $this->getRootDirectory() . '/' . self::TINYMCE_LICENSE_FILE,
            $this->getCopySource() . '/' . self::TINYMCE_LICENSE_NOTE,
        ];

        $filesystem = new Filesystem();

        try {
            $filesystem->remove($files);
            if ($this->event->getIO()->isVeryVerbose()) {
                $this->event->getIO()->write(sprintf('Removed %s and %s', self::TINYMCE_LICENSE_FILE, self::TINYMCE_LICENSE_NOTE));
            }
        } catch (IOException $IOException) {
            $this->event->getIO()->write($IOException->getMessage());
        }
    }
}
