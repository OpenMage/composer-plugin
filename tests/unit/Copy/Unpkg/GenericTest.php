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

namespace OpenMage\ComposerPlugin\Test\Copy\Unpkg;

use OpenMage\ComposerPlugin\Copy\Unpkg\Config;
use OpenMage\ComposerPlugin\Copy\Unpkg\Generic as Subject;
use PHPUnit\Framework\TestCase;

class GenericTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        $config = new Config();
        $config->setUnpkgName('name');
        $config->setUnpkgVersion('version');
        $config->setUnpkgSource('source');
        $config->setUnpkgFiles([]);
        $config->setCopyTarget('target');


        $this->subject = new Subject(null, $config);
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::getUnpkgName()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setCopyTarget()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgFiles()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgName()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgSource()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgVersion()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Generic::__construct()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Generic::getUnpkgName()
     */
    public function testGetUnpkgName(): void
    {
        $this->assertSame('name', $this->subject->getUnpkgName());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::getUnpkgVersion()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setCopyTarget()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgFiles()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgFiles()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgName()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgSource()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgVersion()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Generic::__construct()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Generic::getUnpkgVersion()
     */
    public function testGetUnpkgVersion(): void
    {
        $this->assertSame('version', $this->subject->getUnpkgVersion());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::getUnpkgSource()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setCopyTarget()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgFiles()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgName()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgSource()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgVersion()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Generic::__construct()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Generic::getUnpkgSource()
     */
    public function testGetUnpkgSource(): void
    {
        $this->assertSame('source', $this->subject->getUnpkgSource());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::getUnpkgFiles()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setCopyTarget()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgFiles()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgName()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgSource()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgVersion()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Generic::__construct()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Generic::getUnpkgFiles()
     */
    public function testGetUnpkgFiles(): void
    {
        $this->assertSame([], $this->subject->getUnpkgFiles());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::getCopyTarget()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setCopyTarget()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgFiles()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgName()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgSource()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgVersion()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Generic::__construct()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Generic::getCopyTarget()
     */
    public function testGetCopyTarget(): void
    {
        $this->assertSame('target', $this->subject->getCopyTarget());
    }
}
