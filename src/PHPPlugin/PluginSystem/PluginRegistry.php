<?php
namespace PHPPlugin\PluginSystem;

use PHPPlugin\PluginSystem\Activator\DefaultPluginActivator;
use PHPPlugin\PluginSystem\Activator\ExtensionPointPluginActivator;
use PHPPlugin\PluginSystem\Exception\ActivatorNotFoundException;
use PHPPlugin\PluginSystem\Exception\LocatorNotFoundException;
use PHPPlugin\PluginSystem\Exception\PluginNotFoundException;
use PHPPlugin\PluginSystem\Exception\ResourceNotAvailableException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The plugin registry holds references to all loaded plugins
 * @package PHPPlugin\PluginSystem
 */
class PluginRegistry implements PluginRegistryInterface
{
    const CACHE_KEY = 'com.anddare.plugin.system.pluginPaths';

    /**
     * Plugin storage.
     *
     * @var PluginInterface[]
     */
    private $plugins = [];

    /**
     * Plugin activator instances.
     *
     * @var PluginActivatorInterface[]
     */
    private $pluginActivators = [];

    /**
     * Plugin locator instances.
     *
     * @var PluginLocatorInterface[]
     */
    private $pluginLocators = [];

    /**
     * Extension point registry.
     *
     * @var ExtensionPointRegistryInterface
     */
    private $extensionPointRegistry = null;

    /**
     * Service Locator.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $serviceLocator = null;

    /**
     * Cache component
     * @var AdapterInterface
     */
    private $cache = null;

    /**
     * Plugin loader instance
     * @var PluginLoaderInterface
     */
    private $pluginLoader = null;

    /**
     * Plugin paths on disk
     * @var array
     */
    private $paths = [];

    /**
     * Check if we have a service locator
     * @return boolean
     */
    public function hasServiceLocator()
    {
        return !is_null($this->serviceLocator);
    }

    /**
     * Returns the service locator instance.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
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
     * Checks if there is a cache
     * component registered
     * @return bool
     */
    public function hasCache()
    {
        return !is_null($this->cache);
    }

    /**
     * Returns the cache component
     * @return AdapterInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Set the cache component
     * @param AdapterInterface $cache
     */
    public function setCache(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Returns the plugin loader instance
     * @return PluginLoaderInterface
     */
    public function getPluginLoader()
    {
        return $this->pluginLoader;
    }

    /**
     * Sets the plugin loader instance
     * @param PluginLoaderInterface $loader
     */
    public function setPluginLoader(PluginLoaderInterface $loader)
    {
        $this->pluginLoader = $loader;
    }

    /**
     * Returns the plugin with the given name.
     *
     * @param string $name
     *
     * @throws PluginNotFoundException when the plugin is not found
     *
     * @return PluginInterface
     */
    public function getPlugin($name)
    {
        if (!isset($this->plugins[$name])) {
            throw new PluginNotFoundException('Unknown plugin: '.$name);
        }

        return $this->plugins[$name];
    }

    /**
     * Returns all registered plugins.
     *
     * @return PluginInterface[]
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Returns all plugin paths on disk.
     *
     * @return string[]
     */
    public function getPluginPaths()
    {
        return $this->paths;
    }

    /**
     * Register a plugin in the registry.
     *
     * @param string          $pluginName
     * @param PluginInterface $plugin
     */
    public function register($pluginName, PluginInterface $plugin)
    {
        $this->plugins[$pluginName] = $plugin;
    }

    /**
     * Returns the extension point registry.
     *
     * @return ExtensionPointRegistryInterface
     */
    public function getExtensionPointRegistry()
    {
        if (is_null($this->extensionPointRegistry)) {
            $this->extensionPointRegistry = ComponentAccessor::getInstance()
                    ->getExtensionPointRegistry();
        }

        return $this->extensionPointRegistry;
    }

    /**
     * Returns the plugin activator instances.
     */
    public function getPluginActivators()
    {
        return $this->pluginActivators;
    }

    /**
     * Returns a plugin activator by its className.
     *
     * @param string $className
     *
     * @return PluginActivatorInterface
     *
     * @throws ActivatorNotFoundException when not found
     */
    public function getPluginActivator($className)
    {
        if (!isset($this->pluginActivators[$className])) {
            throw new ActivatorNotFoundException('Cannot find activator of class: '.$className);
        }

        return $this->pluginActivators[$className];
    }

    /**
     * Add a plugin activator instance.
     *
     * @param PluginActivatorInterface $activator
     */
    public function addPluginActivator(PluginActivatorInterface $activator)
    {
        $this->pluginActivators[get_class($activator)] = $activator;
    }

    /**
     * Returns the plugin locators.
     *
     * @return PluginLocatorInterface[]
     */
    public function getPluginLocators()
    {
        return $this->pluginLocators;
    }

    /**
     * Fetch a single plugin locator by className
     * @param string $className
     * @throws LocatorNotFoundException when not found
     * @return PluginLocatorInterface
     */
    public function getPluginLocator($className)
    {
        if (!isset($this->pluginLocators[$className])) {
            throw new LocatorNotFoundException('Cannot find locator of class: '.$className);
        }

        return $this->pluginLocators[$className];
    }

    /**
     * Add a plugin locator.
     *
     * @param PluginLocatorInterface $locator
     */
    public function addPluginLocator(PluginLocatorInterface $locator)
    {
        $this->pluginLocators[get_class($locator)] = $locator;
    }

    /**
     * Locate plugins using the locators.
     * @param boolean $clearCache
     * @return array
     */
    public function locate($clearCache = false)
    {
        $this->paths = [];
        if ($this->hasCache()) {
            $cache = $this->getCache();
            if ($clearCache) {
                $item = $cache->getItem(self::CACHE_KEY);
                $cache->deleteItem(self::CACHE_KEY);
            }
            if ($cache->hasItem(self::CACHE_KEY)) {
                $item = $cache->getItem(self::CACHE_KEY);
                $this->paths = $item->get();
                return $this->paths;
            }
        }
        $locators = $this->getPluginLocators();
        foreach ($locators as $locator) {
            $this->paths = array_merge($this->paths, $locator->locate());
        }
        if ($this->hasCache()) {
            $cache = $this->getCache();
            $item = $cache->getItem(self::CACHE_KEY);
            $item->set($this->paths);
            $cache->save($item);
        }
        return $this->paths;
    }

    /**
     * PluginRegistry initialization.
     */
    protected function addDefaultActivators()
    {
        $this->addPluginActivator(new DefaultPluginActivator());
        $activator = new ExtensionPointPluginActivator($this->getExtensionPointRegistry());
        $this->addPluginActivator($activator);
    }

    /**
     * Load plugins from paths on disk
     * @return void
     */
    protected function loadPlugins()
    {
        $loader = $this->getPluginLoader();
        foreach ($this->paths as $path) {
            $loader->load($path, $this);
        }
    }

    /**
     * Activate the given plugin (with the given name if only a string is given) or
     * when no plugin given activate all plugins.
     *
     * @param PluginInterface|string|null $plugin
     *
     * @throws PluginNotFoundException
     * @throws ResourceNotAvailableException
     */
    public function activate($plugin = null)
    {
        $this->loadPlugins();
        $this->addDefaultActivators();
        if (is_null($plugin)) {
            $plugins = $this->getPlugins();
            foreach ($plugins as $plugin) {
                $this->activate($plugin);
            }
            return;
        }
        if (is_string($plugin)) {
            $plugin = $this->getPlugin($plugin);
        } elseif ($plugin instanceof PluginInterface) {
            $this->register($plugin->getDescriptor()->getName(), $plugin);
        }
        $activators = $this->getPluginActivators();
        foreach ($activators as $activator) {
            $activator->activate($plugin);
        }
    }
}
