<?php

/**
 * @category   OpenMage
 * @package    VendorCopy
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\ComposerPlugin\Copy\Unpkg;

use OpenMage\ComposerPlugin\Copy;

/**
 * Class Npm
 */
class Config implements Copy\CopyFromUnpkgInterface
{
    private string $name    = '';
    private string $version = '';
    private string $source  = '';
    private string $target  = '';

    /**
     * @var string[]
     */
    private array $files    = [];

    /**
     * @param mixed $packageConfig
     * @return array{version: string, source: string, target: string, files: string[]}
     */
    public function getValidatedConfig(string $packageName, $packageConfig): ?array
    {
        if (!is_array($packageConfig)) {
            return null;
        }

        if (!array_key_exists('files', $packageConfig) || !is_array($packageConfig['files'])) {
            return null;
        }

        if (!array_key_exists('version', $packageConfig) || !is_string($packageConfig['version'])) {
            return null;
        }

        $source = '';
        if (array_key_exists('source', $packageConfig) && is_string($packageConfig['source'])) {
            $source = $packageConfig['source'];
        }

        $target = '';
        if (array_key_exists('target', $packageConfig) && is_string($packageConfig['target'])) {
            $target = $packageConfig['target'];
        }

        /** @var string[] $files */
        $files = $packageConfig['files'];
        return [
            'version'   => $packageConfig['version'],
            'source'    => $source,
            'target'    => $target,
            'files'     => $files,
        ];
    }

    public function getUnpkgName(): string
    {
        return $this->name;
    }

    public function setUnpkgName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getUnpkgVersion(): string
    {
        return $this->version;
    }

    public function setUnpkgVersion(string $version): self
    {
        $this->version = $version;
        return $this;
    }

    public function getUnpkgSource(): string
    {
        return $this->source;
    }

    public function setUnpkgSource(string $source): self
    {
        $this->source = $source;
        return $this;
    }

    public function getCopyTarget(): string
    {
        return $this->target;
    }

    public function setCopyTarget(string $target): self
    {
        $this->target = $target;
        return $this;
    }

    public function getUnpkgFiles(): array
    {
        return $this->files;
    }

    /**
     * @param string[] $files
     */
    public function setUnpkgFiles(array $files): self
    {
        $this->files = $files;
        return $this;
    }
}
