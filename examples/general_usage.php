<?php
use PHPPlugin\PluginSystem\ComponentAccessor;

// Or preferably inject with a dependency injection container
$pluginRegistry = ComponentAccessor::getInstance()->getPluginRegistry();

$pluginRegistry
    ->getPluginLocator('PHPPlugin\PluginSystem\Locator\RecursiveDirectoryPluginLocator')
    ->addPath('vendor');

// Now all extensions are available through proxy instances.
$pluginRegistry->activate();

// Or preferably inject with a dependency injection container
$extensionRegistry = ComponentAccessor::getInstance()->getExtensionRegistry();

$extensionRegistry->getExtensionsByType('some.extension.type.identifier');
$extensionRegistry->getExtensionByClassName('Some\Class');