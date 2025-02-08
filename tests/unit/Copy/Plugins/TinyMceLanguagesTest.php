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

use OpenMage\ComposerPlugin\Copy\Plugins\TinyMceLanguages as Subject;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

class TinyMceLanguagesTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        $this->subject = new Subject(null);
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\TinyMceLanguages::getComposerName()
     */
    public function testGetComposerName(): void
    {
        static::assertSame('mklkj/tinymce-i18n', $this->subject->getComposerName());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\TinyMceLanguages::getComposerSource()
     */
    public function testGetComposerSource(): void
    {
        try {
            static::assertSame('langs7', $this->subject->getComposerSource());
        } catch (OutOfBoundsException $exception) {
            static::assertSame('Package "tinymce/tinymce" is not installed', $exception->getMessage());
        }
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\TinyMceLanguages::getComposerFiles()
     */
    public function testGetComposerFiles(): void
    {
        static::assertSame(['*.css', '*.js'], $this->subject->getComposerFiles());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\TinyMceLanguages::getCopyTarget()
     */
    public function testGetCopyTarget(): void
    {
        static::assertSame('js/lib/tinymce/langs', $this->subject->getCopyTarget());
    }
}
