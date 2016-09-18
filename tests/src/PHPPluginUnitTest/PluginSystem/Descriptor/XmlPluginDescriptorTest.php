<?php

namespace PHPPluginUnitTest\PluginSystem;

use PHPPlugin\PluginSystem\Descriptor\XmlPluginDescriptor;
use PHPPlugin\PluginSystem\Exception\ExtensionNotFoundException;

class XmlPluginDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /* @var XmlPluginDescriptor */
    private $subject;

    public function setUp()
    {
        $this->subject = new XmlPluginDescriptor(__DIR__.'/../../../../src/__files/module-dir/unit-test/plugin-test.xml');
    }

    public function testCanReadName()
    {
        $this->assertSame('company/unittest', $this->subject->getName());
    }

    public function testCanGetPluginClass()
    {
        $this->assertSame('PHPPlugin\PluginSystem\UnitTestPlugin', $this->subject->getPluginClass());
    }

    public function testCanGetExtensions()
    {
        $extensions = $this->subject->getExtensions();
        $this->assertInstanceOf('PHPPlugin\PluginSystem\ExtensionDeclarationInterface', @$extensions[0]);
        $this->assertSame('test', $extensions[0]->getType());
        $this->assertSame('PHPPlugin\PluginSystem\UnitTestPlugin', $extensions[0]->getClassName());
        $this->assertContains('test1', $extensions[0]->getAttributes());
    }

    public function testCanGetExtensionsByType()
    {
        $descriptor = $this->subject;
        $extensions = $descriptor->getExtensionsByType('test');
        $this->assertSame(2, count($extensions));
    }

    public function testCanGetExtensionByClassName()
    {
        $descriptor = $this->subject;
        $extension = $descriptor->getExtensionByClassName('PHPPlugin\PluginSystem\UnitTestPlugin2');
        $this->assertSame($extension->getClassName(), 'PHPPlugin\PluginSystem\UnitTestPlugin2');
    }

    /**
     * @expectedException \PHPPlugin\PluginSystem\Exception\ExtensionNotFoundException
     * @expectedExceptionMessage No extension found implemented in class: PHPPlugin\PluginSystem\UnknownPlugin
     */
    public function testRequestingExtensionByClassNameWhenNotKnownThrowsException()
    {
        $descriptor = $this->subject;
        $descriptor->getExtensionByClassName('PHPPlugin\PluginSystem\UnknownPlugin');
    }
}
