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

namespace unit\Copy;

use OpenMage\ComposerPlugin\Copy\AbstractCopyPlugin as Subject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class AbstractCopyPluginTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        $this->subject = $this->getMockForAbstractClass(Subject::class, [], '', false);
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\AbstractCopyPlugin::getFileSystem()
     */
    public function testGetFileSystem(): void
    {
        static::assertInstanceOf(Filesystem::class, $this->subject->getFileSystem());
    }
}
