<?php

namespace PHPPluginUnitTest\PluginSystem;

use PHPPlugin\PluginSystem\ExtensionDeclarationInterface;
use PHPPlugin\PluginSystem\ExtensionPointRegistry;
use PHPPlugin\PluginSystem\PluginRegistryInterface;
use PHPPlugin\PluginSystem\UnitTestPlugin;

class ExtensionPointRegistryTest extends \PHPUnit_Framework_TestCase
{
    /* @var ExtensionPointRegistry */
    private $subject;
    /* @var \stdClass */
    private $extension;
    /* @var ExtensionDeclarationInterface */
    private $declaration;

    public function setUp()
    {
        /* @var PluginRegistryInterface $mockPluginRegistry */
        $mockPluginRegistry = $this->getMockBuilder('PHPPlugin\PluginSystem\PluginRegistryInterface')
                ->getMockForAbstractClass();
        $this->declaration = $this->getMockBuilder('PHPPlugin\PluginSystem\ExtensionDeclarationInterface')
            ->getMockForAbstractClass();
        $this->declaration->expects($this->any())->method('getType')->willReturn('test');
        $this->declaration->expects($this->any())->method('getClassName')->willReturn('PHPPlugin\PluginSystem\UnitTestPlugin');
        $this->declaration->expects($this->any())->method('getAttributes')->willReturn(['test1' => 'test1', 'test2' => 'test2']);
        $this->descriptor = $this->getMockBuilder('PHPPlugin\PluginSystem\PluginDescriptorInterface')
            ->getMockForAbstractClass();
        include_once __DIR__ . '/../../__files/module-dir/unit-test/UnitTestPlugin.php';
        $this->extension = new UnitTestPlugin();
        $this->extension->setDescriptor($this->descriptor);
        $this->descriptor->expects($this->any())->method('getExtensionsByType')->willReturn(array($this->declaration));
        $this->descriptor->expects($this->any())->method('getExtensionByClassName')
                ->with('PHPPlugin\PluginSystem\UnitTestPlugin')
                ->willReturn($this->declaration);
        $mockPluginRegistry->expects($this->any())->method('getPlugins')->willReturn(array('anddare/unittest' => $this->extension));
        $this->subject = new ExtensionPointRegistry($mockPluginRegistry);
        $this->subject->registerExtension('test', $this->extension, $this->declaration);
    }

    public function testCanRegisterAnExtensionPoint()
    {
        $registry = $this->subject;
        $registry->registerExtensionPoint('test', [$this, 'callbackFunction']);
        $this->assertContains($this->extension, $registry->getExtensionsByType('test'));
        $this->assertSame($this->extension, $registry->getExtensionByClassName('PHPPlugin\PluginSystem\UnitTestPlugin'));
    }

    public function callbackFunction($type, $extension, ExtensionDeclarationInterface $declaration)
    {
        $this->assertSame('test', $type);
        $this->assertSame($this->extension, $extension);
        $this->assertSame($this->declaration, $declaration);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage No extension found of class: unknown
     */
    public function testRequestingExtensionOfUnknownClassThrowsException()
    {
        $registry = $this->subject;
        $registry->getExtensionByClassName('unknown');
    }

    public function testCanGetExtensionsByType()
    {
        $registry = $this->subject;
        $extensions = $registry->getExtensionsByType('test');
        $declarations = $registry->getExtensionDeclarationsByType('test');
        $this->assertContains($this->extension, $extensions);
        $this->assertContains($this->declaration, $declarations);
    }

    public function testCanGetExtensionsByClassName()
    {
        $registry = $this->subject;
        $extension = $registry->getExtensionByClassName('PHPPlugin\PluginSystem\UnitTestPlugin');
        $declaration = $registry->getExtensionDeclarationByClassName('PHPPlugin\PluginSystem\UnitTestPlugin');
        $this->assertSame($this->extension, $extension);
        $this->assertSame($this->declaration, $declaration);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage No extension declaration found implemented in: PHPPlugin\PluginSystem\Unknown
     */
    public function testRequestingUnknownExtensionsByClassNameThrowsException()
    {
        $registry = $this->subject;
        $registry->getExtensionDeclarationByClassName('PHPPlugin\PluginSystem\Unknown');
    }

    public function testCanSearchExtensionsByTypeAndAttributes()
    {
        $registry = $this->subject;
        $extensions = $registry->searchExtensions('test', ['test1' => 'test1']);
        $extensions2 = $registry->searchExtensions('test', ['test1' => 'test2']);
        $this->assertContains($this->extension, $extensions);
        $this->assertNotContains($this->extension, $extensions2);
    }

    public function testCanSearchExtensionDeclarationsByTypeAndAttributes()
    {
        $registry = $this->subject;
        $declarations = $registry->searchExtensionDeclarations('test', ['test1' => 'test1']);
        $declarations2 = $registry->searchExtensionDeclarations('test', ['test1' => 'test2']);
        $this->assertContains($this->declaration, $declarations);
        $this->assertNotContains($this->declaration, $declarations2);
    }
}
