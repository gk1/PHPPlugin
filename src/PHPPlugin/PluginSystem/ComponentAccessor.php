<?php

namespace PHPPlugin\PluginSystem;

/**
 * Main entrance point for the plugin system. Holds
 * references to singleton instances of the
 * main plugin system components
 * @package PHPPlugin\PluginSystem
 */
class ComponentAccessor
{
    /**
     * Singleton instance.
     *
     * @var ComponentAccessor
     */
    private static $instance;

    /**
     * Plugin registry instance.
     *
     * @var PluginRegistryInterface
     */
    private $pluginRegistry;

    /**
     * Extension registry instance.
     *
     * @var ExtensionRegistryInterface
     */
    private $extensionRegistry;

    /**
     * Protect the singleton
     * ComponentAccessor constructor.
     */
    private function __construct()
    {
    }

    /**
     * Returns the singleton instance.
     *
     * @return ComponentAccessor
     */
    public static function getInstance()
    {
        self::$instance = self::$instance ?: new self();

        return self::$instance;
    }

    /**
     * Returns the plugin registry instance.
     *
     * @return PluginRegistryInterface
     */
    public function getPluginRegistry()
    {
        $this->pluginRegistry = $this->pluginRegistry ?: new PluginRegistry();

        return $this->pluginRegistry;
    }

    /**
     * Returns the extension registry instance.
     *
     * @return ExtensionRegistryInterface
     */
    public function getExtensionRegistry()
    {
        $this->extensionRegistry = $this->extensionRegistry ?: $this->buildExtensionRegistry();

        return $this->extensionRegistry;
    }

    /**
     * @return ExtensionRegistry
     */
    protected function buildExtensionRegistry()
    {
        return new ExtensionRegistry($this->getPluginRegistry());
    }
}
