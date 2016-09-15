<?php
namespace PHPPluginUnitTest\PluginSystem\Activator;


use PHPPlugin\PluginSystem\Activator\DefaultExtensionProxy;

class DefaultExtensionProxyTest extends \PHPUnit_Framework_TestCase
{

    private $subject = null;

    public function setUp()
    {
        $this->subject = new DefaultExtensionProxy('stdClass');
    }

    public function tearDown()
    {
        $this->subject = null;
    }

    public function testCanInstantiateAnObject()
    {
        $proxy = $this->subject;
        $this->assertSame('stdClass', $proxy->getClassName());
        $this->assertInstanceOf('stdClass', $proxy->getInstance());
    }

}