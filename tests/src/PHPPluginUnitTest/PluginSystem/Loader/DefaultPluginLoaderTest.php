<?php
namespace PHPPluginUnitTest\PluginSystem\Loader;

use PHPPlugin\PluginSystem\Descriptor\XmlPluginDescriptor;
use PHPPlugin\PluginSystem\Loader\DefaultPluginLoader;

class DefaultPluginLoaderTest extends \PHPUnit_Framework_TestCase
{

    private $subject = null;

    public function setUp()
    {
        $this->subject = new DefaultPluginLoader(XmlPluginDescriptor::class, 'plugin-test.xml');
    }

    public function tearDown()
    {
        $this->subject = null;
    }

    public function testCanGetTheDescriptorClass()
    {
        $this->assertSame(XmlPluginDescriptor::class, $this->subject->getDescriptorClass());
    }

    public function testCanGetThePluginFilename()
    {
        $this->assertSame('plugin-test.xml', $this->subject->getPluginFilename());
    }

    protected function getMockRegistry()
    {
        $registry = $this->getMockBuilder('PHPPlugin\PluginSystem\PluginRegistryInterface')
                ->getMockForAbstractClass();
        return $registry;
    }

    public function testCanLoadPlugins()
    {
        $loader = $this->subject;
        $registry = $this->getMockRegistry();
        $registry->expects($this->once())->method('register')->with('company/unittest', $this->anything());
        $loader->load(realpath('./tests/src/__files/module-dir/unit-test') . '/', $registry);
    }

}