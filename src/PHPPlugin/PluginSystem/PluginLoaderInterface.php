<?php

namespace PHPPlugin\PluginSystem;

/**
 * Plugin Loaders are used by the plugin registry
 * to load a plugin and it's descriptor from a specific plugin path on disk
 * @package PHPPlugin\PluginSystem
 */
interface PluginLoaderInterface
{
    /**
     * Returns the expected plugin filename.
     *
     * @return string
     */
    public function getPluginFilename();

    /**
     * Load plugin from the given plugin path.
     *
     * @param string                  $pluginPath
     * @param PluginRegistryInterface $registry
     */
    public function load($pluginPath, PluginRegistryInterface $registry);
}
