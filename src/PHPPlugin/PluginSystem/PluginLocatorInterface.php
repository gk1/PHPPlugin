<?php

namespace PHPPlugin\PluginSystem;

/**
 * Locators are used by the plugin registry
 * to locate plugin paths on disk
 * @package PHPPlugin\PluginSystem
 */
interface PluginLocatorInterface
{
    /**
     * Locate the plugins
     */
    public function locate();

}
