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
 * Class Config
 */
class Config implements Copy\CopyFromUnpkgInterface
{
    public const CONFIG_FILES   = 'files';
    public const CONFIG_SOURCE  = 'source';
    public const CONFIG_TARGET  = 'target';
    public const CONFIG_VERSION = 'version';

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
    public function getValidatedConfig($packageConfig): ?array
    {
        if (!is_array($packageConfig)) {
            return null;
        }

        if (!$this->validateConfigFiles($packageConfig)) {
            return null;
        }

        if (!$this->validateConfigVersion($packageConfig)) {
            return null;
        }

        $source = '';
        if (array_key_exists(self::CONFIG_SOURCE, $packageConfig) && is_string($packageConfig[self::CONFIG_SOURCE])) {
            $source = $packageConfig[self::CONFIG_SOURCE];
        }

        $target = '';
        if (array_key_exists(self::CONFIG_TARGET, $packageConfig) && is_string($packageConfig[self::CONFIG_TARGET])) {
            $target = $packageConfig[self::CONFIG_TARGET];
        }

        /** @var string[] $files */
        $files = $packageConfig[self::CONFIG_FILES];
        return [
            'version'   => $packageConfig[self::CONFIG_VERSION],
            'source'    => $source,
            'target'    => $target,
            'files'     => $files,
        ];
    }

    /**
     * @param array<mixed> $packageConfig
     */
    private function validateConfigFiles(array $packageConfig): bool
    {
        return array_key_exists(self::CONFIG_FILES, $packageConfig) && is_array($packageConfig[self::CONFIG_FILES]);
    }

    /**
     * @param array<mixed> $packageConfig
     */
    private function validateConfigVersion(array $packageConfig): bool
    {
        return array_key_exists(self::CONFIG_VERSION, $packageConfig) && is_string($packageConfig[self::CONFIG_VERSION]);
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
