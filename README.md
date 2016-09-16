# PHPPlugin system
A simple plugin system supporting xml plugin files with extension points. The system implements
different locator types to find plugins and different activators to activate plugins.

### Example usage

$pluginRegistry = ComponentAccessor::getInstance()->getPluginRegistry();

$locator = new RecursiveDirectoryPluginLocator(array('path' => './plugins'));

$pluginRegistry->addPluginLocator($locator);

$pluginRegistry->addPluginActivator(new DefaultPluginActivator());

$pluginRegistry->locate();

$pluginRegistry->activate();
