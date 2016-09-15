<?php
namespace PHPPluginUnitTest\PluginSystem;


class AbstractPluginTest extends \PHPUnit_Framework_TestCase
{

    private $subject = null;

    public function setUp()
    {
        require_once __DIR__ . '/ConcretePlugin.php';
        $this->subject = new ConcretePlugin();
    }

    public function tearDown()
    {
        $this->subject = null;
    }

    public function testCanSetADescriptor()
    {
        $plugin = $this->subject;
        $mockDescriptor = $this->getMockBuilder('PHPPlugin\PluginSystem\PluginDescriptorInterface')
                ->getMockForAbstractClass();
        $plugin->setDescriptor($mockDescriptor);
        $this->assertSame($mockDescriptor, $plugin->getDescriptor());
    }

    public function testCanSetAPluginPath()
    {
        $plugin = $this->subject;
        $plugin->setPluginPath('.');
        $this->assertSame('.', $plugin->getPluginPath());
    }

}