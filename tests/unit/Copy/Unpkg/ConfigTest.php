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

use Generator;
use OpenMage\ComposerPlugin\Copy\Unpkg\Config as Subject;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        $this->subject = new Subject(null);
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::getValidatedConfig()
     * @dataProvider provideGetValidatedConfig
     */
    public function testGetValidatedConfig(?array $expectedResult, $packageConfig): void
    {
        $this->assertSame($expectedResult, $this->subject->getValidatedConfig($packageConfig));
    }

    public function provideGetValidatedConfig(): Generator
    {
        yield 'invalid' => [
            null,
            null,
        ];

        yield 'empty' => [
            null,
            [],
        ];

        yield 'no files' => [
            null,
            [
                'files' => null,
                'version' => '1',
            ],
        ];

        yield 'no version' => [
            null,
            [
                'files' => [
                    'test.file',
                ],
                'version' => null,
            ],
        ];

        yield 'empty source/target' => [
            [
                'version' => '1',
                'source'  => '',
                'target'  => '',
                'files'   => [
                    'test.file',
                ],
            ],
            [
                'files' => [
                    'test.file',
                ],
                'version' => '1',
            ],
        ];

        yield 'complete' => [
            [
                'version' => '1',
                'source'  => 'source',
                'target'  => 'target',
                'files'   => [
                    'test.file',
                ],
            ],
            [
                'files' => [
                    'test.file',
                ],
                'version'   => '1',
                'source'    => 'source',
                'target'    => 'target',
            ],
        ];

        yield 'target sub-directory' => [
            [
                'version' => '1',
                'source'  => 'source',
                'target'  => 'target',
                'files'   => [
                    'test.file',
                ],
            ],
            [
                'files' => [
                    'test.file',
                ],
                'version'   => '1',
                'source'    => 'source',
                'target'    => './../target',
            ],
        ];

        yield 'target w/ spaces' => [
            [
                'version' => '1',
                'source'  => 'source',
                'target'  => 'target',
                'files'   => [
                    'test.file',
                ],
            ],
            [
                'files' => [
                    'test.file',
                ],
                'version'   => '1',
                'source'    => 'source',
                'target'    => '  target  ',
            ],
        ];
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::getUnpkgName()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgName()
     */
    public function testUnpkgName(): void
    {
        $source = '';
        $this->subject->setUnpkgName($source);
        $this->assertSame($source, $this->subject->getUnpkgName());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::getUnpkgVersion()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgVersion()
     */
    public function testUnpkgVersion(): void
    {
        $source = '';
        $this->subject->setUnpkgVersion($source);
        $this->assertSame($source, $this->subject->getUnpkgVersion());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::getUnpkgSource()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgSource()
     */
    public function testUnpkgSource(): void
    {
        $source = '';
        $this->subject->setUnpkgSource($source);
        $this->assertSame($source, $this->subject->getUnpkgSource());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::getCopyTarget()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setCopyTarget()
     */
    public function testCopyTarget(): void
    {
        $target = '';
        $this->subject->setCopyTarget($target);
        $this->assertSame($target, $this->subject->getCopyTarget());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::getUnpkgFiles()
     * @covers \OpenMage\ComposerPlugin\Copy\Unpkg\Config::setUnpkgFiles()
     */
    public function testUnpkgFiles(): void
    {
        $files = [];
        $this->subject->setUnpkgFiles($files);
        $this->assertSame($files, $this->subject->getUnpkgFiles());
    }
}
