<?php
namespace PHPPlugin\PluginSystem;

/**
 * Plugin activators perform all needed
 * actions after a plugin is instantiated
 * and registered
 * @package PHPPlugin\PluginSystem
 */
interface PluginActivatorInterface
{
    /**
     * Activate the given plugin.
     *
     * @param PluginInterface $plugin
     *
     * @throws PluginLoadException when failing to activate the plugin
     */
    public function activate(PluginInterface $plugin);
}
