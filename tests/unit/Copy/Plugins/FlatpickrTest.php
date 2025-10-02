<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\ComposerPlugin\Test\Copy\Plugins;

use OpenMage\ComposerPlugin\Copy\Plugins\Flatpickr as Subject;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

class FlatpickrTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        $this->subject = new Subject(null);
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\Flatpickr::getUnpkgName()
     */
    public function testGetUnpkgName(): void
    {
        self::assertSame('flatpickr', $this->subject->getUnpkgName());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\Flatpickr::getUnpkgVersion()
     */
    public function testGetUnpkgVersion(): void
    {
        try {
            self::assertIsString($this->subject->getUnpkgVersion());
        } catch (OutOfBoundsException $exception) {
            self::assertSame('Package "nnnick/chartjs" is not installed', $exception->getMessage());
        }
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\Flatpickr::getUnpkgSource()
     */
    public function testGetUnpkgSource(): void
    {
        self::assertSame('dist', $this->subject->getUnpkgSource());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\Flatpickr::getUnpkgFiles()
     */
    public function testGetUnpkgFiles(): void
    {
        $result = [
            0 => 'flatpickr.min.css',
            1 => 'flatpickr.min.js',
        ];
        self::assertSame($result, $this->subject->getUnpkgFiles());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\Flatpickr::getCopyTarget()
     */
    public function testGetCopyTarget(): void
    {
        self::assertSame('js/lib/flatpickr', $this->subject->getCopyTarget());
    }
}
