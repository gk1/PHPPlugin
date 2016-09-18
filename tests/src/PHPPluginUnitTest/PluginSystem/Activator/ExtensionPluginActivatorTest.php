<?php

namespace PHPPluginUnitTest\PluginSystem\Activator;

use PHPPlugin\PluginSystem\Activator\ExtensionPluginActivator;
use PHPPlugin\PluginSystem\ExtensionRegistryInterface;
use PHPPlugin\PluginSystem\PluginInterface;

class ExtensionPluginActivatorTest extends \PHPUnit_Framework_TestCase
{
    /* @var ExtensionPluginActivator */
    private $subject;

    /* @var ExtensionRegistryInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mockRegistry;

    public function setUp()
    {
        $this->mockRegistry = $this->getMockBuilder('PHPPlugin\PluginSystem\ExtensionRegistryInterface')
                ->getMockForAbstractClass();
        $this->subject = new ExtensionPluginActivator($this->mockRegistry);
    }

    /**
     *
     */
    public function testCanActivatePlugins()
    {
        $activator = $this->subject;
        $mockDeclaration = $this->getMockBuilder('PHPPlugin\PluginSystem\ExtensionDeclarationInterface')
                ->getMockForAbstractClass();
        $mockDeclaration->expects($this->atLeastOnce())->method('getClassName')->willReturn('TestExtension');
        $mockDeclaration->expects($this->once())->method('getType')->willReturn('test');

        $mockDeclaration2 = $this->getMockBuilder('PHPPlugin\PluginSystem\ExtensionDeclarationInterface')
            ->getMockForAbstractClass();
        $mockDeclaration2->expects($this->atLeastOnce())->method('getClassName')->willReturn('TestExtension2');

        $mockDescriptor = $this->getMockBuilder('PHPPlugin\PluginSystem\PluginDescriptorInterface')
                ->getMockForAbstractClass();
        $mockDescriptor->expects($this->once())->method('getExtensions')->willReturn([$mockDeclaration, $mockDeclaration2]);
        /* @var PluginInterface|\PHPUnit_Framework_MockObject_MockObject $mockPlugin */
        $mockPlugin = $this->getMockBuilder('PHPPlugin\PluginSystem\PluginInterface')
                ->getMockForAbstractClass();
        $mockPlugin->expects($this->once())->method('getDescriptor')
                ->willReturn($mockDescriptor);
        $this->mockRegistry->expects($this->once())->method('registerExtension')
                ->withConsecutive(['test', $this->anything(), $mockDeclaration]);
        $activator->filterClass('TestExtension2');
        $activator->activate($mockPlugin);
    }
}
