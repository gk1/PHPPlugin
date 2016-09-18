<?php
namespace PHPPlugin\PluginSystem;

/**
 * Contract all plugins must implement
 * @package PHPPlugin\PluginSystem
 */
interface PluginInterface
{
    /**
     * Returns the plugin descriptor.
     *
     * @return PluginDescriptorInterface
     */
    public function getDescriptor();

    /**
     * Set the plugin descriptor.
     *
     * @param PluginDescriptorInterface $descriptor
     * @return void
     */
    public function setDescriptor(PluginDescriptorInterface $descriptor);

    /**
     * Returns the plugin path.
     *
     * @return string
     */
    public function getPluginPath();

    /**
     * Sets the plugin path on disk.
     *
     * @param string $path
     * @return void
     */
    public function setPluginPath($path);

    /**
     * Activate the current plugin.
     */
    public function activate();
}
