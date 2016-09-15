<?php
namespace PHPPlugin\PluginSystem\Activator;

use PHPPlugin\PluginSystem\ExtensionDeclarationInterface;
use PHPPlugin\PluginSystem\ExtensionPointRegistryInterface;
use PHPPlugin\PluginSystem\Locator\ClassInstantiatingServiceLocator;
use PHPPlugin\PluginSystem\PluginActivatorInterface;
use PHPPlugin\PluginSystem\PluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Registers the extension points of loaded plugins
 * @package PHPPlugin\PluginSystem\Activator
 */
class ExtensionPointPluginActivator implements PluginActivatorInterface
{
    /**
     * Extension point registry.
     *
     * @var ExtensionPointRegistryInterface
     */
    private $extensionPointRegistry;

    /**
     * Service locator instance.
     *
     * @var ContainerInterface
     */
    private $serviceLocator;

    /**
     * Filtered extension classNames
     * @var string[]
     */
    private $filters = [];

    /**
     * ExtensionPointPluginActivator constructor.
     *
     * @param ExtensionPointRegistryInterface $registry
     */
    public function __construct(ExtensionPointRegistryInterface $registry)
    {
        $this->extensionPointRegistry = $registry;
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
                $this->extensionPointRegistry->registerExtension($declaration->getType(), $extension, $declaration);
            }
        }
    }

    /**
     * Returns the service locator instance.
     *
     * @return ContainerInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator ?: new ClassInstantiatingServiceLocator();
    }

    /**
     * Set the service locator instance.
     *
     * @param ContainerInterface $locator
     */
    public function setServiceLocator(ContainerInterface $locator)
    {
        $this->serviceLocator = $locator;
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

        return $this->getServiceLocator()->get($className);
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

        return new DefaultExtensionProxy($className, $this->serviceLocator);
    }
}
