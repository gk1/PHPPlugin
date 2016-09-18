<?php
namespace PHPPlugin\PluginSystem;

/**
 * The extension registry holds
 * all information on registered plugin extensions
 * @package PHPPlugin\PluginSystem
 */
interface ExtensionRegistryInterface
{
    /**
     * Register an extension.
     *
     * @param string $type
     * @param $extension
     * @param ExtensionDeclarationInterface $declaration
     * @return void
     */
    public function registerExtension($type, $extension, ExtensionDeclarationInterface $declaration);

    /**
     * Register an extension point callback
     * All extension with the specified type
     * will be provided to the callback function
     * when plugins are activated.
     *
     * The signature of the callback must be <object> (extension), <extensionDeclarationInterface>
     *
     * @param string   $type
     * @param callable $callback
     * @return void
     */
    public function registerExtensionPoint($type, callable $callback);

    /**
     * Returns all extensions declarations of a specific type.
     *
     * @param string $type
     *
     * @return ExtensionDeclarationInterface[]
     */
    public function getExtensionsByType($type);

    /**
     * Returns the extension declaration with a specific className.
     *
     * @param string $className
     *
     * @return ExtensionDeclarationInterface
     */
    public function getExtensionByClassName($className);
}
