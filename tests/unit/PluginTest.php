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

namespace OpenMage\ComposerPlugin\Test;

use OpenMage\ComposerPlugin\Plugin as Subject;
use PHPUnit\Framework\TestCase;

class PluginTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        $this->subject = new Subject();
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Plugin::getSubscribedEvents()
     */
    public function testGetSubscribedEvents(): void
    {
        $events = [
            'post-install-cmd' => [
                0 => [
                    0 => 'processCopy',
                ],
            ],
            'post-update-cmd' => [
                0 => [
                    0 => 'processCopy',
                ],
            ],
        ];
        self::assertSame($events, $this->subject->getSubscribedEvents());
    }

    /**
     * @covers \OpenMage\ComposerPlugin\Copy\AbstractCopyPlugin::getComposerPackage()
     * @covers \OpenMage\ComposerPlugin\Copy\AbstractCopyPlugin::getEvent()
     * @covers \OpenMage\ComposerPlugin\Copy\AbstractCopyPlugin::getInstalledComposerPackage()
     * @covers \OpenMage\ComposerPlugin\Copy\AbstractCopyPlugin::processComposerInstall()
     * @covers \OpenMage\ComposerPlugin\Copy\AbstractCopyPlugin::processUnpkgInstall()
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\ChartJs::getComposerName()
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\Flatpickr::getUnpkgVersion()
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\FlowJs::getComposerName()
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\JQuery::getComposerName()
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\TinyMce::processComposerInstall()
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\TinyMceLanguages::getComposerName()
     * @covers \OpenMage\ComposerPlugin\Copy\Plugins\TinyMceLanguages::processComposerInstall()
     * @covers \OpenMage\ComposerPlugin\Plugin::getClassName()
     * @covers \OpenMage\ComposerPlugin\Plugin::getFilenames()
     * @covers \OpenMage\ComposerPlugin\Plugin::getFullNamespace()
     * @covers \OpenMage\ComposerPlugin\Plugin::getPlugins()
     * @covers \OpenMage\ComposerPlugin\Plugin::getUnpkgPackagesFromConfig()
     * @covers \OpenMage\ComposerPlugin\Plugin::processCopy()
     */
    public function testProcessCopy(): void
    {
        self::assertNull($this->subject->processCopy(null));
    }
}
