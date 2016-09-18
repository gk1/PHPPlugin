<?php
namespace PHPPlugin\PluginSystem\Activator;

use PHPPlugin\PluginSystem\ServiceContainer\MockServiceContainer;
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
    private $serviceContainer;

    /**
     * DefaultExtensionProxy constructor.
     *
     * @param string             $className
     * @param ContainerInterface $serviceContainer
     */
    public function __construct($className, $serviceContainer = null)
    {
        $this->className = $className;
        $this->setServiceContainer($serviceContainer ?: new MockServiceContainer());
    }

    /**
     * Returns the extension FQCN
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Sets the service container instance
     * @param ContainerInterface $locator
     */
    public function setServiceContainer(ContainerInterface $locator)
    {
        $this->serviceContainer = $locator;
    }

    public function getInstance()
    {
        $className = $this->className;

        return $this->serviceContainer->get($className);
    }
}
