<?php

/**
 * @category   OpenMage
 * @package    VendorCopy
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\ComposerPlugin\Copy;

/**
 * CopyFromComposerInterface
 */
interface CopyFromComposerInterface
{
    /**
     * Composer name
     */
    public function getComposerName(): string;

    public function getComposerSource(): string;

    /**
     * @return string[]
     */
    public function getComposerFiles(): array;
}
