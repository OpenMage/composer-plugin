<?php

/**
 * @category   OpenMage
 * @package    VendorCopy
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\ComposerPlugin\Copy\Plugins;

use OpenMage\ComposerPlugin\Copy;

/**
 * Class FlowJs
 */
class FlowJs extends Copy\AbstractCopyPlugin implements Copy\CopyFromComposerInterface
{
    public function getComposerName(): string
    {
        return 'flowjs/flowjs';
    }

    public function getComposerSource(): string
    {
        return 'dist';
    }

    public function getComposerFiles(): array
    {
        return ['*.js'];
    }

    public function getCopyTarget(): string
    {
        return 'js/lib/uploader';
    }
}
