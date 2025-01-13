<?php

/**
 * @category   OpenMage
 * @package    VendorCopy
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\ComposerPlugin\Copy\Plugins;

use Composer\InstalledVersions;
use OpenMage\ComposerPlugin\Copy;

/**
 * Class TinyMceLanguages
 */
class TinyMceLanguages extends Copy\AbstractCopyPlugin implements Copy\CopyFromComposerInterface
{
    public const TINYMCE = 'tinymce/tinymce';

    public function getComposerName(): string
    {
        return 'mklkj/tinymce-i18n';
    }

    /**
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function getComposerSource(): string
    {
        /** @var string $version */
        $version = InstalledVersions::getVersion(self::TINYMCE);
        return 'langs' . $version[0];
    }

    public function getComposerFiles(): array
    {
        return ['*.css', '*.js'];
    }

    public function getCopyTarget(): string
    {
        return 'js/lib/tinymce/langs';
    }

    /**
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function processComposerInstall(): void
    {
        if (!InstalledVersions::isInstalled(self::TINYMCE)) {
            return;
        }
        parent::processComposerInstall();
    }
}
