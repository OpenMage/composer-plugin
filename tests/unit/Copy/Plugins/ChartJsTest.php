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

use OpenMage\ComposerPlugin\Copy\Plugins\ChartJs as Subject;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

class ChartJsTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        $this->subject = new Subject(null);
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\ChartJs::getUnpkgName()
     */
    public function testGetUnpkgName(): void
    {
        self::assertSame('chart.js', $this->subject->getUnpkgName());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\ChartJs::getUnpkgVersion()
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\ChartJs::getComposerName()
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
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\ChartJs::getUnpkgSource()
     */
    public function testGetUnpkgSource(): void
    {
        self::assertSame('dist', $this->subject->getUnpkgSource());
    }


    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\ChartJs::getUnpkgFiles()
     */
    public function testGetUnpkgFiles(): void
    {
        $result = [
            0 => 'chart.umd.js',
            1 => 'chart.umd.js.map',
            2 => 'helpers.js',
            3 => 'helpers.js.map',
        ];
        self::assertSame($result, $this->subject->getUnpkgFiles());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\ChartJs::getComposerName()
     */
    public function testGetComposerName(): void
    {
        self::assertSame('nnnick/chartjs', $this->subject->getComposerName());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\ChartJs::getComposerSource()
     */
    public function testGetComposerSource(): void
    {
        self::assertSame('dist', $this->subject->getComposerSource());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\ChartJs::getComposerFiles()
     */
    public function testGetComposerFiles(): void
    {
        self::assertSame(['*.js', '*.map'], $this->subject->getComposerFiles());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\ChartJs::getCopyTarget()
     */
    public function testGetCopyTarget(): void
    {
        self::assertSame('js/lib/chartjs', $this->subject->getCopyTarget());
    }
}
