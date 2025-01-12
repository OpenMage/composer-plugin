<?php

/**
 * @category   OpenMage
 * @package    VendorCopy
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\ComposerPlugin\Copy\Unpkg;

use Composer\Script\Event;
use OpenMage\ComposerPlugin\Copy;

/**
 * Class Npm
 */
class Generic extends Copy\AbstractCopyPlugin implements Copy\CopyFromUnpkgInterface
{
    private Config $config;

    public function __construct(Event $event, Config $config)
    {
        $this->config = $config;

        parent::__construct($event);
    }

    public function getUnpkgName(): string
    {
        return $this->config->getUnpkgName();
    }

    public function getUnpkgVersion(): string
    {
        return $this->config->getUnpkgVersion();
    }

    public function getUnpkgSource(): string
    {
        return $this->config->getUnpkgSource();
    }

    public function getUnpkgFiles(): array
    {
        return $this->config->getUnpkgFiles();
    }

    public function getCopyTarget(): string
    {
        return $this->config->getCopyTarget();
    }
}
