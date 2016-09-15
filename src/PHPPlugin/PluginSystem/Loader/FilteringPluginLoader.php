<?php
namespace PHPPlugin\PluginSystem\Loader;

use PHPPlugin\PluginSystem\PluginDescriptorInterface;
use PHPPlugin\PluginSystem\PluginInterface;
use PHPPlugin\PluginSystem\PluginRegistryInterface;

/**
 * Extension of the default plugin loader capable
 * of filtering plugins before loading
 * @package PHPPlugin\PluginSystem\Loader
 */
class FilteringPluginLoader extends DefaultPluginLoader
{
    private $filters = array();

    /**
     * Returns all pluginNames that will
     * not be loaded
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Add a pluginName that should
     * not be loaded
     * @param string $pluginName
     */
    public function addFilter($pluginName)
    {
        $this->filters[] = $pluginName;
    }

    /**
     * Register the plugin in the registry
     * @param PluginRegistryInterface $registry
     * @param PluginDescriptorInterface $descriptor
     * @param PluginInterface $plugin
     */
    protected function register($registry, $descriptor, $plugin)
    {
        if (in_array($descriptor->getName(), $this->getFilters())) {
            return;
        }
        $registry->register($descriptor->getName(), $plugin);
    }

}
