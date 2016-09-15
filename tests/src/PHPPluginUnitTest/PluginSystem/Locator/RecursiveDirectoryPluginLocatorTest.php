<?php

namespace PHPPluginUnitTest\PluginSystem\Loader;

use PHPPlugin\PluginSystem\ComponentAccessor;
use PHPPlugin\PluginSystem\Loader\DefaultPluginLoader;
use PHPPlugin\PluginSystem\Locator\RecursiveDirectoryPluginLocator;
use Symfony\Component\Console\Descriptor\XmlDescriptor;

class RecursiveDirectoryPluginLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function testCanSuccessfullyLoad()
    {
        $locator = new RecursiveDirectoryPluginLocator(['path' => __DIR__.'/../../../__files', 'pluginFilename' => 'plugin-test.xml']);
        $paths = $locator->locate();
        $this->assertContains(__DIR__  . '/../../../__files/module-dir/unit-test/', $paths);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage No path provided in options
     */
    public function testNoPathProvidedThrowsException()
    {
        $loader = new RecursiveDirectoryPluginLocator(['pluginFilename' => 'plugin.xml']);
        $loader->locate();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage No pluginFilename provided in options
     */
    public function testNoPluginFilenameProvidedThrowsException()
    {
        $loader = new RecursiveDirectoryPluginLocator(['path' => '.']);
        $loader->locate();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Given plugin path cannot be found
     */
    public function testInvalidPluginPathThrowsException()
    {
        $loader = new RecursiveDirectoryPluginLocator(['path' => __DIR__.'/../../../__files/unknown', 'pluginFilename' => 'plugin.xml']);
        $loader->locate();
    }

}
