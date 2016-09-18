<?php
namespace PHPPlugin\PluginSystem\Activator;

use PHPPlugin\PluginSystem\ServiceLocator\ClassInstantiatingServiceLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Proxy class for lazy loading plugin instances
 * @package PHPPlugin\PluginSystem\Activator
 */
class DefaultExtensionProxy implements ExtensionProxyInterface
{
    /* @var string */
    private $className;
    /* @var ContainerInterface */
    private $serviceLocator;

    /**
     * DefaultExtensionProxy constructor.
     *
     * @param string             $className
     * @param ContainerInterface $serviceLocator
     */
    public function __construct($className, $serviceLocator = null)
    {
        $this->className = $className;
        $this->setServiceLocator($serviceLocator ?: new ClassInstantiatingServiceLocator());
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function setServiceLocator(ContainerInterface $locator)
    {
        $this->serviceLocator = $locator;
    }

    public function getInstance()
    {
        $className = $this->className;

        return $this->serviceLocator->get($className);
    }
}
