<?php
namespace PHPPlugin\PluginSystem\Activator;

use PHPPlugin\PluginSystem\PluginActivatorInterface;
use PHPPlugin\PluginSystem\PluginInterface;

/**
 * Calls the Plugin::activate() method on loaded plugins
 * @package PHPPlugin\PluginSystem\Activator
 */
class DefaultPluginActivator implements PluginActivatorInterface
{
    public function activate(PluginInterface $plugin)
    {
        $plugin->activate();
    }
}
