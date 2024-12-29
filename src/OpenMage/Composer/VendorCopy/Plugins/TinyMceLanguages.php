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

use Composer\InstalledVersions;
use OpenMage\Composer\VendorCopy\AbstractPlugin;

/**
 * Class TinyMceLanguages
 */
class TinyMceLanguages extends AbstractPlugin
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
