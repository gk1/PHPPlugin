<?php
namespace PHPPlugin\PluginSystem\Activator;

use PHPPlugin\PluginSystem\ExtensionDeclarationInterface;
use PHPPlugin\PluginSystem\ExtensionRegistryInterface;
use PHPPlugin\PluginSystem\ServiceContainer\MockServiceContainer;
use PHPPlugin\PluginSystem\PluginActivatorInterface;
use PHPPlugin\PluginSystem\PluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Registers the extensions of loaded plugins
 * @package PHPPlugin\PluginSystem\Activator
 */
class ExtensionPluginActivator implements PluginActivatorInterface
{
    /**
     * Extension registry.
     *
     * @var ExtensionRegistryInterface
     */
    private $extensionRegistry;

    /**
     * Service container instance.
     *
     * @var ContainerInterface
     */
    private $serviceContainer;

    /**
     * Filtered extension classNames
     * @var string[]
     */
    private $filters = [];

    /**
     * ExtensionPluginActivator constructor.
     *
     * @param ExtensionRegistryInterface $registry
     */
    public function __construct(ExtensionRegistryInterface $registry)
    {
        $this->extensionRegistry = $registry;
    }

    /**
     * Returns all extension classNames
     * that are filtered and not loaded
     * as extensions
     * @return string[]
     */
    public function getFilteredClasses()
    {
        return $this->filters;
    }

    /**
     * Filter an extension class so it is not loaded
     * @param string $className
     */
    public function filterClass($className)
    {
        $this->filters[] = $className;
    }

    /**
     * Register extensions and activate the plugin.
     *
     * @param PluginInterface $plugin
     */
    public function activate(PluginInterface $plugin)
    {
        $extensions = $plugin->getDescriptor()->getExtensions();
        foreach ($extensions as $declaration) {
            $extension = $this->createExtensionProxy($declaration);
            if (!in_array($declaration->getClassName(), $this->getFilteredClasses())) {
                $this->extensionRegistry->registerExtension($declaration->getType(), $extension, $declaration);
            }
        }
    }

    /**
     * Returns the service container instance.
     *
     * @return ContainerInterface
     */
    public function getServiceContainer()
    {
        return $this->serviceContainer ?: new MockServiceContainer();
    }

    /**
     * Set the service container instance.
     *
     * @param ContainerInterface $locator
     */
    public function setServiceContainer(ContainerInterface $locator)
    {
        $this->serviceContainer = $locator;
    }

    /**
     * Create the extension instance.
     *
     * @param ExtensionDeclarationInterface $declaration
     *
     * @return mixed
     */
    protected function createExtension(ExtensionDeclarationInterface $declaration)
    {
        $className = $declaration->getClassName();

        return $this->getServiceContainer()->get($className);
    }

    /**
     * Create an extension proxy instance.
     *
     * @param ExtensionDeclarationInterface $declaration
     *
     * @return ExtensionProxyInterface
     */
    protected function createExtensionProxy(ExtensionDeclarationInterface $declaration)
    {
        $className = $declaration->getClassName();

        return new DefaultExtensionProxy($className, $this->getServiceContainer());
    }
}
