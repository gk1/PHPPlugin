<?php

namespace PHPPluginUnitTest\PluginSystem;

use PHPPlugin\PluginSystem\Exception\ActivatorNotFoundException;
use PHPPlugin\PluginSystem\Loader\DefaultPluginLoader;
use PHPPlugin\PluginSystem\PluginActivatorInterface;
use PHPPlugin\PluginSystem\PluginInterface;
use PHPPlugin\PluginSystem\PluginLocatorInterface;
use PHPPlugin\PluginSystem\PluginRegistry;

class PluginRegistryTest extends \PHPUnit_Framework_TestCase
{
    /* @var PluginRegistry */
    private $subject;

    public function setUp()
    {
        $this->subject = new PluginRegistry();
    }

    public function testCanSetAndGetAPluginLocatorInstance()
    {
        $registry = $this->subject;
        /* @var PluginLocatorInterface $mockLocator */
        $mockLocator = $this->getMockBuilder('PHPPlugin\PluginSystem\PluginLocatorInterface')
                ->getMockForAbstractClass();
        $registry->addPluginLocator($mockLocator);
        $this->assertContains($mockLocator, $registry->getPluginLocators());
    }

    public function testCanSetAndGetAPluginActivatorInstance()
    {
        $registry = $this->subject;
        /* @var PluginActivatorInterface $mockActivator */
        $mockActivator = $this->getMockBuilder('PHPPlugin\PluginSystem\PluginActivatorInterface')
            ->getMockForAbstractClass();
        $registry->addPluginActivator($mockActivator);
        $className = get_class($mockActivator);
        $this->assertSame($mockActivator, $registry->getPluginActivator($className));
        $this->assertContains($mockActivator, $registry->getPluginActivators());
    }

    /**
     * @expectedException \PHPPlugin\PluginSystem\Exception\ActivatorNotFoundException
     * @expectedExceptionMessage Cannot find activator of class: unknownClass
     */
    public function testRequestingAnActivatorThatsNotRegisteredThrowsException()
    {
        $registry = $this->subject;
        $registry->getPluginActivator('unknownClass');
    }

    public function testCanSuccessfullyRegisterAPlugin()
    {
        $registry = $this->subject;
        /* @var PluginInterface $mockPlugin */
        $mockPlugin = $this->getMockBuilder('PHPPlugin\PluginSystem\PluginInterface')
                ->getMockForAbstractClass();
        $registry->register('test', $mockPlugin);
        $this->assertSame($mockPlugin, $registry->getPlugin('test'));
        $this->assertContains($mockPlugin, $registry->getPlugins());
    }

    /**
     * @expectedException \PHPPlugin\PluginSystem\Exception\PluginNotFoundException
     * @expectedExceptionMessage Unknown plugin: unknown
     */
    public function testRequestingAnUnknownPluginThrowsException()
    {
        $registry = $this->subject;
        $registry->getPlugin('unknown');
    }

    public function testCanSuccessfullyActivateAPlugin()
    {
        $registry = $this->subject;
        /* @var PluginActivatorInterface|\PHPUnit_Framework_MockObject_MockObject $mockActivator */
        $mockActivator = $this->getMockBuilder('PHPPlugin\PluginSystem\PluginActivatorInterface')
                ->getMockForAbstractClass();
        $mockPlugin = $this->getMockBuilder('PHPPlugin\PluginSystem\PluginInterface')
            ->getMockForAbstractClass();
        $mockDescriptor = $this->getMockBuilder('PHPPlugin\PluginSystem\PluginDescriptorInterface')
                ->getMockForAbstractClass();
        $mockDescriptor->expects($this->any())->method('getExtensions')->willReturn([]);
        $mockDescriptor->expects($this->any())->method('getName')->willReturn('test');
        $mockPlugin->expects($this->any())->method('getDescriptor')->willReturn($mockDescriptor);
        $mockActivator->expects($this->once())->method('activate')
            ->with($mockPlugin);
        $registry->addPluginActivator($mockActivator);
        $registry->activate($mockPlugin);
    }

    public function testCanSetAPluginLoader()
    {
        $registry = $this->subject;
        $this->assertNull($registry->getPluginLoader());
        $mockLoader = $this->getMockBuilder('PHPPlugin\PluginSystem\Loader\DefaultPluginLoader')
                ->disableOriginalConstructor()
                ->getMock();
        $registry->setPluginLoader($mockLoader);
        $this->assertSame($mockLoader, $registry->getPluginLoader());
    }

    public function testCanSetACache()
    {
        $registry = $this->subject;
        $mockCache = $this->getMockBuilder('Symfony\Component\Cache\Adapter\AdapterInterface')
                ->getMockForAbstractClass();
        $this->assertFalse($registry->hasCache());
        $registry->setCache($mockCache);
        $this->assertTrue($registry->hasCache());
        $this->assertSame($mockCache, $registry->getCache());
    }

    public function testCanSetAServiceLocator()
    {
        $registry = $this->subject;
        $mockLocator = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                ->getMockForAbstractClass();
        $this->assertFalse($registry->hasServiceLocator());
        $registry->setServiceLocator($mockLocator);
        $this->assertTrue($registry->hasServiceLocator());
        $this->assertSame($mockLocator, $registry->getServiceLocator());
    }

    public function testCanLocatePluginsFromCache()
    {
        $registry = $this->subject;
        $cache = $this->getMockBuilder('Symfony\Component\Cache\Adapter\AdapterInterface')
                ->getMockForAbstractClass();
        $cache->expects($this->any())->method('hasItem')->with(PluginRegistry::CACHE_KEY)->willReturn(true);
        $mockItem = $this->getMockBuilder('Psr\Cache\CacheItemInterface')
            ->getMockForAbstractClass();
        $mockItem->expects($this->any())->method('get')->willReturn(['.']);
        $cache->expects($this->any())->method('getItem')->with(PluginRegistry::CACHE_KEY)->willReturn($mockItem);
        $registry->setCache($cache);
        $paths = $registry->locate();
        $this->assertContains('.', $paths);
        $this->assertContains('.', $registry->getPluginPaths());
    }

    public function testCanLocatePluginsFromCacheWithClearedCache()
    {
        $registry = $this->subject;
        $cache = $this->getMockBuilder('Symfony\Component\Cache\Adapter\AdapterInterface')
            ->getMockForAbstractClass();
        $mockItem = $this->getMockBuilder('Psr\Cache\CacheItemInterface')
                ->getMockForAbstractClass();
        $mockItem->expects($this->any())->method('get')->willReturn(['.']);
        $cache->expects($this->atleast(1))->method('getItem')->with(PluginRegistry::CACHE_KEY)->willReturn($mockItem);
        $cache->expects($this->once())->method('deleteItem')->with(PluginRegistry::CACHE_KEY);
        $cache->expects($this->any())->method('hasItem')->with(PluginRegistry::CACHE_KEY)->willReturn(false);
        $cache->expects($this->once())->method('save')->with($this->anything());
        $registry->setCache($cache);
        $paths = $registry->locate(true);
        $this->assertNotContains('.', $paths);
    }

    public function testCanFetchAPluginLocatorByClassName()
    {
        $registry = $this->subject;
        $mockLocator = $this->getMockBuilder('PHPPlugin\PluginSystem\PluginLocatorInterface')
            ->getMockForAbstractClass();
        $registry->addPluginLocator($mockLocator);
        $locator = $registry->getPluginLocator(get_class($mockLocator));
        $this->assertSame($mockLocator, $locator);
    }

    /**
     * @expectedException \PHPPlugin\PluginSystem\Exception\LocatorNotFoundException
     * @expectedExceptionMessage Cannot find locator of class: UnknownClass
     */
    public function testFetchingAnUnknownPluginLocatorThrowsException()
    {
        $registry = $this->subject;
        $mockLocator = $this->getMockBuilder('PHPPlugin\PluginSystem\PluginLocatorInterface')
            ->getMockForAbstractClass();
        $registry->addPluginLocator($mockLocator);
        $registry->getPluginLocator('UnknownClass');
    }

}
