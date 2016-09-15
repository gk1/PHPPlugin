<?php

namespace PHPPlugin\PluginSystem;

/**
 * Abstract implementation of a plugin
 * with some often needed functions
 * @package PHPPlugin\PluginSystem
 */
class AbstractPlugin implements PluginInterface
{
    /* @var PluginDescriptorInterface */
    private $descriptor;
    /* @var string */
    private $pluginPath;

    /**
     * Returns the plugin descriptor.
     *
     * @return PluginDescriptorInterface
     */
    public function getDescriptor()
    {
        return $this->descriptor;
    }

    /**
     * Sets the plugin descriptor.
     *
     * @param PluginDescriptorInterface $descriptor
     */
    public function setDescriptor(PluginDescriptorInterface $descriptor)
    {
        $this->descriptor = $descriptor;
    }

    /**
     * Returns the plugin path on disk.
     *
     * @return string
     */
    public function getPluginPath()
    {
        return $this->pluginPath;
    }

    /**
     * Sets the plugin path on disk.
     *
     * @param string $path
     */
    public function setPluginPath($path)
    {
        $this->pluginPath = $path;
    }

    /**
     * Activate the plugin.
     */
    public function activate()
    {
    }
}
