<?php

namespace PHPPlugin\PluginSystem;

use PHPPlugin\PluginSystem\Exception\PluginLoadException;
use PHPPlugin\PluginSystem\Exception\PluginNotFoundException;
use PHPPlugin\PluginSystem\Exception\PluginRegistrationException;

/**
 * The Plugin Registry is the main API point
 * for locating, registering and activating plugins.
 */
interface PluginRegistryInterface
{
    /**
     * Returns the plugin with
     * the specified name.
     *
     * @param string $name
     *
     * @return PluginInterface
     *
     * @throws PluginNotFoundException when the plugin is not found
     */
    public function getPlugin($name);

    /**
     * Returns all registered plugins.
     *
     * @return PluginInterface[]
     */
    public function getPlugins();

    /**
     * Returns all plugin paths on disk.
     *
     * @return string[]
     */
    public function getPluginPaths();

    /**
     * Register a plugin in the registry.
     *
     * @param string          $pluginName
     * @param PluginInterface $plugin
     *
     * @throws PluginRegistrationException when failing to register
     */
    public function register($pluginName, PluginInterface $plugin);

    /**
     * Returns the extension point registry.
     *
     * @return ExtensionPointRegistryInterface
     */
    public function getExtensionPointRegistry();

    /**
     * Returns the plugin activator instances.
     *
     * @return PluginActivatorInterface[]
     */
    public function getPluginActivators();

    /**
     * Returns a plugin activator by its className.
     *
     * @param $className
     *
     * @return PluginActivatorInterface
     */
    public function getPluginActivator($className);

    /**
     * Add a plugin activator instance.
     *
     * @param PluginActivatorInterface $activator
     */
    public function addPluginActivator(PluginActivatorInterface $activator);

    /**
     * Returns the plugin locator instances.
     *
     * @return PluginLocatorInterface[]
     */
    public function getPluginLocators();

    /**
     * Returns a plugin locator by its className.
     *
     * @param $className
     *
     * @return PluginLocatorInterface
     */
    public function getPluginLocator($className);

    /**
     * Add a plugin locator instance.
     *
     * @param PluginLocatorInterface $locator
     */
    public function addPluginLocator(PluginLocatorInterface $locator);

    /**
     * Locate plugin instances.
     */
    public function locate();

    /**
     * Activate the plugin (with the specified name).
     *
     * @param string|PluginInterface $name
     *
     * @throws PluginLoadException when failing to activate the plugin
     */
    public function activate($name);
}
