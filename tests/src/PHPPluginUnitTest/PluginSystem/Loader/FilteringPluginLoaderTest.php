<?php
namespace PHPPluginUnitTest\PluginSystem\Loader;

use PHPPlugin\PluginSystem\Descriptor\XmlPluginDescriptor;
use PHPPlugin\PluginSystem\Loader\FilteringPluginLoader;

class FilteringPluginLoaderTest extends \PHPUnit_Framework_TestCase
{

    private $subject = null;

    public function setUp()
    {
        $this->subject = new FilteringPluginLoader(XmlPluginDescriptor::class, 'plugin-test.xml');
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
        $loader->addFilter('anddare/unknown');
        $this->assertContains('anddare/unknown', $loader->getFilters());
        $registry = $this->getMockRegistry();
        $registry->expects($this->once())->method('register')->with('anddare/unittest', $this->anything());
        $loader->load(realpath('./tests/src/__files/module-dir/unit-test') . '/', $registry);
    }

    public function testCanFilterPluginLoading()
    {
        $loader = $this->subject;
        $loader->addFilter('anddare/unittest');
        $this->assertContains('anddare/unittest', $loader->getFilters());
        $registry = $this->getMockRegistry();
        $registry->expects($this->exactly(0))->method('register')->with('anddare/unittest', $this->anything());
        $loader->load(realpath('./tests/src/__files/module-dir/unit-test') . '/', $registry);
    }

}