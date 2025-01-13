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

use OpenMage\ComposerPlugin\Copy\Plugins\FlowJs as Subject;
use PHPUnit\Framework\TestCase;

class FlowJsTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        $this->subject = new Subject(null);
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\FlowJs::getComposerName()
     */
    public function testGetComposerName(): void
    {
        static::assertSame('flowjs/flowjs', $this->subject->getComposerName());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\FlowJs::getComposerSource()
     */
    public function testGetComposerSource(): void
    {
        static::assertSame('dist', $this->subject->getComposerSource());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\FlowJs::getComposerFiles()
     */
    public function testGetComposerFiles(): void
    {
        static::assertSame(['*.js'], $this->subject->getComposerFiles());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\FlowJs::getCopyTarget()
     */
    public function testGetCopyTarget(): void
    {
        static::assertSame('js/lib/uploader', $this->subject->getCopyTarget());
    }
}
