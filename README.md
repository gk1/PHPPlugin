[buildStatus]: https://scrutinizer-ci.com/g/gheevel/PHPPlugin/badges/build.png?b=master "Build status"
[buildScore]: https://scrutinizer-ci.com/g/gheevel/PHPPlugin/badges/quality-score.png?b=master "Build Quality Score"
[buildCoverage]: https://scrutinizer-ci.com/g/gheevel/PHPPlugin/badges/coverage.png?b=master "Build Coverage"

![alt text][buildScore]
![alt text][buildStatus]
![alt-text][buildCoverage]

# PHPPlugin system
A simple > PHP5.3 plugin system supporting plugin descriptor files and extension points. It is inspired by the eclipse and jira plugin architectures. 
In combination with a dependency manager (Composer), a module system (for example Symfony bundles) and a depency injection framework (for example the Symfony service container) 
this provides a very powerfull way of building a pluggable architecture in a PHP application. It can be easily extended to filter loaded plugins 
based on your own criteria which allows for different levels op application functionalities (maybe based on subscription level).

## Terminology
- Registry :: Container of shared objects (for example plugins and extension points)
- Locators :: Objects that locate installed plugins
- Activators :: Objects that provide different ways to activate plugins
- Loader :: Loads and parses  plugin declaration and register it on the plugin registry

## Example usage

```php
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
$extensionRegistry = ComponentAccessor::getInstance()->getExtensionPointRegistry();

$extensionRegistry->getExtensionsByType('some.extension.type.identifier');
$extensionRegistry->getExtensionByClassName('Some\Class');
```