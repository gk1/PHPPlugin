<?php
namespace PHPPlugin\PluginSystem\Activator;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Contract for extension proxies
 * @package PHPPlugin\PluginSystem\Activator
 */
interface ExtensionProxyInterface
{
    /**
     * ExtensionProxyInterface constructor.
     *
     * @param string $className
     */
    public function __construct($className);

    /**
     * Returns the className to instantiate.
     *
     * @return string
     */
    public function getClassName();

    /**
     * Set a service locator instance.
     *
     * @param ContainerInterface $locator
     */
    public function setServiceLocator(ContainerInterface $locator);

    /**
     * Returns the extension instance.
     *
     * @return mixed
     */
    public function getInstance();
}
