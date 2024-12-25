<?php

declare(strict_types=1);

namespace OpenMage\Composer;

/**
 * VendorCopyInterface interface
 */
interface VendorCopyInterface
{
    /**
     * Composer vendor/name
     */
    public function getVendorName(): string;

    /**
     * Path to source files inside vendor directory
     */
    public function getCopySource(): string;

    /**
     * Path to copy target
     */
    public function getCopyTarget(): string;

    /**
     * Filename patternst to copy
     *
     * @return string[]
     */
    public function getFilesByName(): array;
}
