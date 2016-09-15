<?php
namespace PHPPlugin\PluginSystem;

/**
 * A plugin descriptor holds all
 * plugin information needed for the plugin system
 * to operate successfully
 * @package PHPPlugin\PluginSystem
 */
interface PluginDescriptorInterface
{
    /**
     * Returns the unique plugin name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the plugin classname.
     *
     * @return string
     */
    public function getPluginClass();

    /**
     * Returns the extensions declared in the plugin.
     *
     * @return ExtensionDeclarationInterface[]
     */
    public function getExtensions();

    /**
     * Returns all extension declarations of a specific type.
     *
     * @param string $type
     *
     * @return ExtensionDeclarationInterface[]
     */
    public function getExtensionsByType($type);

    /**
     * Returns the extension with a specific className.
     *
     * @param string $className
     *
     * @return ExtensionDeclarationInterface
     */
    public function getExtensionByClassName($className);
}
