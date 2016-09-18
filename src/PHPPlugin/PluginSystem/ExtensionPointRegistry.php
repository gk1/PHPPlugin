<?php
namespace PHPPlugin\PluginSystem;

use PHPPlugin\PluginSystem\Activator\ExtensionProxyInterface;

/**
 * The extension point registry holds information
 * about the registered plugin extensions
 * @package PHPPlugin\PluginSystem
 */
class ExtensionPointRegistry implements ExtensionPointRegistryInterface
{
    /**
     * Extension point callbacks.
     *
     * @var callable[]
     */
    private $callbacks = [];

    /**
     * Plugin Registry instance.
     *
     * @var PluginRegistryInterface
     */
    private $pluginRegistry = null;

    /**
     * Extension instances.
     *
     * @var array
     */
    private $extensions = [];

    /**
     * ExtensionPointRegistry constructor.
     *
     * @param PluginRegistryInterface $registry
     */
    public function __construct(PluginRegistryInterface $registry)
    {
        $this->pluginRegistry = $registry;
    }

    /**
     * Register an extension.
     *
     * @param string $type
     * @param $extension
     * @param ExtensionDeclarationInterface $declaration
     */
    public function registerExtension($type, $extension, ExtensionDeclarationInterface $declaration)
    {
        if (!isset($this->extensions[$type])) {
            $this->extensions[$type] = [];
        }
        $class = $extension instanceof ExtensionProxyInterface ? $extension->getClassName() : get_class($extension);
        $this->extensions[$type][$class] = $extension;

        if (isset($this->callbacks[$type]) && is_array($this->callbacks[$type])) {
            foreach ($this->callbacks[$type] as $callback) {
                call_user_func($callback, $type, $extension, $declaration);
            }
        }
    }

    /**
     * Register an extension point callback.
     *
     * @param string   $type
     * @param callable $callback
     */
    public function registerExtensionPoint($type, callable $callback)
    {
        if (!isset($this->callbacks[$type])) {
            $this->callbacks[$type] = array();
        }
        array_push($this->callbacks[$type], $callback);
    }

    /**
     * Returns all extension declarations of a specific type.
     * @param string $type
     * @return ExtensionDeclarationInterface[]
     */
    public function getExtensionDeclarationsByType($type)
    {
        $plugins = $this->pluginRegistry->getPlugins();
        $declarations = [];
        foreach ($plugins as $plugin) {
            $descriptor = $plugin->getDescriptor();
            $pluginExtensions = $descriptor->getExtensionsByType($type);
            $declarations = array_merge($declarations, $pluginExtensions);
        }
        return $declarations;
    }

    /**
     * Returns the extension declaration with a specific className.
     * @param string $className
     * @return ExtensionDeclarationInterface
     * @throws \Exception
     */
    public function getExtensionDeclarationByClassName($className)
    {
        $plugins = $this->pluginRegistry->getPlugins();
        foreach ($plugins as $plugin) {
            $descriptor = $plugin->getDescriptor();
            try {
                $pluginExtension = $descriptor->getExtensionByClassName($className);
                return $pluginExtension;
            } catch (\Exception $e) {
                // Skip
            }
        }
        throw new \Exception('No extension declaration found implemented in: '.$className);
    }

    /**
     * Search for extension declarations by specifying a type
     * and expected attribute combinations
     * @param string $type
     * @param array $attributes
     * @return ExtensionDeclarationInterface[]
     */
    public function searchExtensionDeclarations($type, array $attributes)
    {
        $result = [];
        $declarations = $this->getExtensionDeclarationsByType($type);
        foreach ($declarations as $declaration) {
            $extensionAttributes = $declaration->getAttributes();
            foreach ($attributes as $key => $value) {
                if (!isset($extensionAttributes[$key]) || $extensionAttributes[$key] != $value) {
                    continue 2;
                }
            }
            $result[] = $declaration;
        }
        return $result;
    }

    /**
     * Returns all extensions declarations of a specific type.
     * @param string $type
     * @return array
     */
    public function getExtensionsByType($type)
    {
        if (isset($this->extensions[$type]) && is_array($this->extensions[$type])) {
            foreach ($this->extensions[$type] as $key => $extension) {
                $this->handleProxyInstance($extension, $type, $key);
            }
        }
        return isset($this->extensions[$type]) ? $this->extensions[$type] : [];
    }

    /**
     * Returns the extension declaration with a specific className.
     * @param string $className
     * @return mixed
     * @throws \Exception when not found
     */
    public function getExtensionByClassName($className)
    {
        foreach ($this->extensions as $type => $extensions) {
            foreach ($extensions as $cN => $extension) {
                if ($cN == $className) {
                    $this->handleProxyInstance($extension, $type, $cN);
                    return $this->extensions[$type][$cN];
                }
            }
        }
        throw new \Exception('No extension found of class: '.$className);
    }

    /**
     * Get the instance from a extension proxy instance
     * @param $extension
     * @param $type
     * @param $key
     */
    private function handleProxyInstance($extension, $type, $key)
    {
        if ($extension instanceof ExtensionProxyInterface) {
            $this->injectAvailableServiceLocator($extension);
            $this->extensions[$type][$key] = $extension->getInstance();
        }
    }

    /**
     * Inject the service locator if available
     * @param ExtensionProxyInterface $extension
     */
    private function injectAvailableServiceLocator(ExtensionProxyInterface $extension)
    {
        if ($this->pluginRegistry->hasServiceLocator()) {
            $extension->setServiceLocator($this->pluginRegistry->getServiceLocator());
        }
    }

    /**
     * Search extensions by specifying a type and an expected attribute combination
     * @param string $type
     * @param array $attributes
     * @return array
     */
    public function searchExtensions($type, array $attributes)
    {
        $result = [];
        $extensions = $this->getExtensionsByType($type);
        foreach ($extensions as $extension) {
            $declaration = $this->getExtensionDeclarationByClassName(get_class($extension));
            $declarationAttributes = $declaration->getAttributes();
            foreach ($attributes as $key => $value) {
                if (!isset($declarationAttributes[$key]) || $declarationAttributes[$key] != $value) {
                    continue 2;
                }
            }
            $result[] = $extension;
        }
        return $result;
    }

}