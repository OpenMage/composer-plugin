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

use OpenMage\ComposerPlugin\Copy\Plugins\TinyMce as Subject;
use PHPUnit\Framework\TestCase;

class TinyMceTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        $this->subject = new Subject(null);
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\TinyMce::getComposerName()
     */
    public function testGetComposerName(): void
    {
        $this->assertSame('tinymce/tinymce', $this->subject->getComposerName());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\TinyMce::getComposerSource()
     */
    public function testGetComposerSource(): void
    {
        $this->assertSame('', $this->subject->getComposerSource());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\TinyMce::getComposerFiles()
     */
    public function testGetComposerFiles(): void
    {
        $this->assertSame(['*.css', '*.js'], $this->subject->getComposerFiles());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\TinyMce::getCopyTarget()
     */
    public function testGetCopyTarget(): void
    {
        $this->assertSame('js/lib/tinymce', $this->subject->getCopyTarget());
    }
}
