<?php
namespace PHPPlugin\PluginSystem;

/**
 * An extension declaration holds all
 * information about a plugin's extension
 * @package PHPPlugin\PluginSystem
 */
interface ExtensionDeclarationInterface
{
    /**
     * Returns the extension type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the extension implementation className.
     *
     * @return string
     */
    public function getClassName();

    /**
     * Returns the extension attributes.
     *
     * @return string[]
     */
    public function getAttributes();
}
