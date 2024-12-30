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

use OpenMage\Composer\VendorCopy\AbstractPlugin;

/**
 * Class JQuery
 */
class JQuery extends AbstractPlugin
{
    public function getComposerPackageName(): string
    {
        return 'components/jquery';
    }

    public function getCopySource(): string
    {
        return '';
    }

    public function getCopyTarget(): string
    {
        return 'js/lib/jquery';
    }

    public function getFilesByName(): array
    {
        return ['*.map', '*.js'];
    }
}
