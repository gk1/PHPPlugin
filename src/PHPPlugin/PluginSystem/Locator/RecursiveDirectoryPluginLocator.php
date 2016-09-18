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
     * @var array
     */
    private $path = [];

    public function __construct(array $options)
    {
        if (!isset($options['path'])) {
            throw new \LogicException('No path provided in options');
        }
        if (!isset($options['pluginFilename'])) {
            throw new \LogicException('No pluginFilename provided in options');
        }
        $this->addPath($options['path']);
        $this->options = $options;
    }

    /**
     * Returns the locator paths
     *
     * @return array
     */
    public function getPaths()
    {
        return $this->path;
    }

    /**
     * Add a locator path
     *
     * @param $path
     * @return RecursiveDirectoryPluginLocator
     */
    public function addPath($path)
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException('Given plugin path cannot be found');
        }
        $this->path[] = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        return $this;
    }

    /**
     * Recursively load all plugins in the given directory
     * The plugin in the directory should register itself
     * with the plugin registry.
     */
    public function locate()
    {
        $paths = [];
        $searchPaths = $this->getPaths();
        while ($path = array_pop($searchPaths)) {
            $dir = new \DirectoryIterator($path);
            while ($dir->valid()) {
                if (!$dir->isDir() || $dir->isDot()) {
                    $dir->next();
                    continue;
                }
                $pluginPath = $path . $dir->current() . DIRECTORY_SEPARATOR;
                if (!$this->isPluginPath($pluginPath)) {
                    $searchPaths[] = $pluginPath;
                    $dir->next();
                    continue;
                }
                $paths[] = $pluginPath;
                $dir->next();
            }
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
