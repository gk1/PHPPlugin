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
     * Extension point registry instance.
     *
     * @var ExtensionPointRegistryInterface
     */
    private $extensionPointRegistry;

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
     * Returns the extension point registry instance.
     *
     * @return ExtensionPointRegistryInterface
     */
    public function getExtensionPointRegistry()
    {
        $this->extensionPointRegistry = $this->extensionPointRegistry ?: $this->buildExtensionPointRegistry();

        return $this->extensionPointRegistry;
    }

    /**
     * @return ExtensionPointRegistry
     */
    protected function buildExtensionPointRegistry()
    {
        return new ExtensionPointRegistry($this->getPluginRegistry());
    }
}
