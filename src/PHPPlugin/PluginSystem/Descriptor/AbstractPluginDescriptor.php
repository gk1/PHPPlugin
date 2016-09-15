<?php
namespace PHPPlugin\PluginSystem\Descriptor;

use PHPPlugin\PluginSystem\Exception\ExtensionNotFoundException;
use PHPPlugin\PluginSystem\ExtensionDeclarationInterface;
use PHPPlugin\PluginSystem\PluginDescriptorInterface;

/**
 * Abstract implementation of a plugin descriptor, holds some
 * helper functions for descriptors
 * @package PHPPlugin\PluginSystem\Descriptor
 */
abstract class AbstractPluginDescriptor implements PluginDescriptorInterface
{
    /**
     * Returns all extensions of a specific type.
     *
     * @param string $type
     *
     * @return ExtensionDeclarationInterface[]
     */
    public function getExtensionsByType($type)
    {
        $result = [];
        $extensions = $this->getExtensions();
        foreach ($extensions as $extension) {
            if ($extension->getType() == $type) {
                $result[] = $extension;
            }
        }
        return $result;
    }

    /**
     * Searches the extension with the given className.
     *
     * @param string $className
     *
     * @return ExtensionDeclarationInterface
     *
     * @throws \Exception when not found
     */
    public function getExtensionByClassName($className)
    {
        $extensions = $this->getExtensions();
        foreach ($extensions as $extension) {
            if ($extension->getClassName() == $className) {
                return $extension;
            }
        }

        throw new ExtensionNotFoundException('No extension found implemented in class: '.$className);
    }
}
