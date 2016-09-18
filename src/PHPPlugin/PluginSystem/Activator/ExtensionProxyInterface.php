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
     * @return void
     */
    public function __construct($className);

    /**
     * Returns the className to instantiate.
     *
     * @return string
     */
    public function getClassName();

    /**
     * Set a service container instance.
     *
     * @param ContainerInterface $locator
     * @return void
     */
    public function setServiceContainer(ContainerInterface $locator);

    /**
     * Returns the extension instance.
     *
     * @return mixed
     */
    public function getInstance();
}
