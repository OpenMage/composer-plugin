<?php

declare(strict_types=1);

namespace OpenMage\Composer\Plugin;

use Composer\InstalledVersions;

/**
 * Class TinyMceLanguages
 */
class TinyMceLanguages extends AbstractVendorCopyPlugin
{
    public const TINYMCE = 'tinymce/tinymce';

    public function getVendorName(): string
    {
        return 'mklkj/tinymce-i18n';
    }

    public function getCopySource(): string
    {
        /** @var string $version */
        $version = InstalledVersions::getVersion(self::TINYMCE);
        return 'langs' . $version[0];
    }

    public function getCopyTarget(): string
    {
        return 'js/tinymce/langs';
    }

    public function getFilesByName(): array
    {
        return ['*.css', '*.js'];
    }

    public function copyFiles(): void
    {
        if (!InstalledVersions::isInstalled(self::TINYMCE)) {
            return;
        }
        parent::copyFiles();
    }
}
