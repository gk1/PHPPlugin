<?php

namespace PHPPluginUnitTest\PluginSystem\Activator;

use PHPPlugin\PluginSystem\Activator\ExtensionPointPluginActivator;
use PHPPlugin\PluginSystem\ExtensionPointRegistryInterface;
use PHPPlugin\PluginSystem\PluginInterface;

class ExtensionPointPluginActivatorTest extends \PHPUnit_Framework_TestCase
{
    /* @var ExtensionPointPluginActivator */
    private $subject;
    /* @var ExtensionPointRegistryInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $mockRegistry;

    public function setUp()
    {
        $this->mockRegistry = $this->getMockBuilder('PHPPlugin\PluginSystem\ExtensionPointRegistryInterface')
                ->getMockForAbstractClass();
        $this->subject = new ExtensionPointPluginActivator($this->mockRegistry);
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
