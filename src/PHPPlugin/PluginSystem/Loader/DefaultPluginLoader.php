<?php
namespace PHPPlugin\PluginSystem\Loader;

use PHPPlugin\PluginSystem\Exception\ResourceNotAvailableException;
use PHPPlugin\PluginSystem\PluginDescriptorInterface;
use PHPPlugin\PluginSystem\PluginInterface;
use PHPPlugin\PluginSystem\PluginLoaderInterface;
use PHPPlugin\PluginSystem\PluginRegistryInterface;

/**
 * Base plugin loader class. Loads the descriptor, instantiates the plugin class
 * and registers it with the plugin registry
 * @package PHPPlugin\PluginSystem\Loader
 */
class DefaultPluginLoader implements PluginLoaderInterface
{
    /**
     * Expected plugin filename.
     *
     * @var string
     */
    private $pluginFilename;

    /**
     * ClassName of the descriptor class.
     *
     * @var string
     */
    private $descriptorClass;

    /**
     * @param string $descriptorClass
     * @param string $pluginFilename
     */
    public function __construct($descriptorClass, $pluginFilename)
    {
        $this->descriptorClass = $descriptorClass;
        $this->pluginFilename = $pluginFilename;
    }

    /**
     * Returns the expected plugin filename.
     *
     * @return string
     */
    public function getPluginFilename()
    {
        return $this->pluginFilename;
    }

    /**
     * Returns the descriptor className.
     *
     * @return string
     */
    public function getDescriptorClass()
    {
        return $this->descriptorClass;
    }

    /**
     * Load the plugin class from the given plugin path.
     *
     * @param $pluginPath
     * @param PluginRegistryInterface $registry
     *
     * @throws ResourceNotAvailableException
     */
    public function load($pluginPath, PluginRegistryInterface $registry)
    {
        $descriptorClass = $this->getDescriptorClass();
        /* @var PluginDescriptorInterface $descriptor */
        $descriptor = new $descriptorClass($pluginPath.$this->getPluginFilename());
        $pluginClass = $descriptor->getPluginClass();
        $pluginFile = $this->baseClassName($pluginClass).'.php';
        if (file_exists($pluginPath . $pluginFile)) {
            include_once($pluginPath . $pluginFile);
        }

        $plugin = new $pluginClass();
        if ($plugin instanceof PluginInterface) {
            $plugin->setDescriptor($descriptor);
            $plugin->setPluginPath($pluginPath);
            $this->register($registry, $descriptor, $plugin);
        }
    }

    /**
     * Register the plugin in the registry
     * @param PluginRegistryInterface $registry
     * @param PluginDescriptorInterface $descriptor
     * @param PluginInterface $plugin
     */
    protected function register($registry, $descriptor, $plugin)
    {
        $registry->register($descriptor->getName(), $plugin);
    }

    /**
     * Returns the base name of a FQCN.
     *
     * @param $className
     *
     * @return string
     */
    private function baseClassName($className)
    {
        $parts = explode('\\', $className);

        return array_pop($parts);
    }
}
