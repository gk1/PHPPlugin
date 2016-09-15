<?php
namespace PHPPlugin\PluginSystem\Locator;

use PHPPlugin\PluginSystem\Exception\ResourceNotAvailableException;
use PHPPlugin\PluginSystem\PluginLoaderInterface;
use PHPPlugin\PluginSystem\PluginLocatorInterface;
use PHPPlugin\PluginSystem\PluginRegistryInterface;

/**
 * Plugin locator capable of recursively searching plugins in directories
 * @package PHPPlugin\PluginSystem\Locator
 */
class RecursiveDirectoryPluginLocator implements PluginLocatorInterface
{

    /**
     * Locator options.
     *
     * @var array
     */
    private $options = [];

    /**
     * Locator path.
     *
     * @var string
     */
    private $path;

    public function __construct(array $options)
    {
        if (!isset($options['path'])) {
            throw new \LogicException('No path provided in options');
        }
        if (!isset($options['pluginFilename'])) {
            throw new \LogicException('No pluginFilename provided in options');
        }
        $this->setPath($options['path']);
        $this->options = $options;
    }

    /**
     * Returns the locator path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the locator path.
     *
     * @param $path
     */
    public function setPath($path)
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException('Given plugin path cannot be found');
        }

        $this->path = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }

    /**
     * Recursively load all plugins in the given directory
     * The plugin in the directory should register itself
     * with the plugin registry.
     */
    public function locate()
    {
        $paths = [];
        $path = $this->getPath();
        $dir = new \DirectoryIterator($path);
        while ($dir->valid()) {
            if (!$dir->isDir() || $dir->isDot()) {
                $dir->next();
                continue;
            }
            $pluginPath = $path.$dir->current().DIRECTORY_SEPARATOR;
            if (!$this->isPluginPath($pluginPath)) {
                $this->setPath($pluginPath);
                $paths = array_merge($paths, $this->locate());
                $dir->next();
                continue;
            }
            $paths[] = $pluginPath;
            $dir->next();
        }
        return $paths;
    }

    /**
     * Check if the given directory contains a plugin.
     *
     * @param $pluginPath
     *
     * @return bool
     */
    private function isPluginPath($pluginPath)
    {
        $dir = new \DirectoryIterator($pluginPath);
        while ($dir->valid()) {
            if ($dir->isDir()) {
                $dir->next();
                continue;
            }
            if ($dir->getFilename() == $this->options['pluginFilename']) {
                return true;
            }
            $dir->next();
        }

        return false;
    }
}
