<?php
namespace PHPPlugin\PluginSystem\Descriptor;

use PHPPlugin\PluginSystem\PluginDescriptorInterface;

/**
 * Implementation of a plugin descriptor based on XML files
 * @package PHPPlugin\PluginSystem\Descriptor
 */
class XmlPluginDescriptor extends AbstractPluginDescriptor implements PluginDescriptorInterface
{
    /**
     * Plugin XML.
     *
     * @var \DOMDocument
     */
    private $xml;

    public function __construct($filename)
    {
        $this->xml = new \DOMDocument();
        $this->xml->load($filename);
    }

    /**
     * Returns the unique plugin name.
     *
     * @return string
     */
    public function getName()
    {
        $name = $this->xml->getElementsByTagName('name');
        return (string) $name->item(0)->nodeValue;
    }

    /**
     * Returns the plugin class name.
     *
     * @return string
     */
    public function getPluginClass()
    {
        $class = $this->xml->getElementsByTagName('pluginClass');
        return (string) $class->item(0)->nodeValue;
    }

    /**
     * Returns the extensions declared by the plugin.
     *
     * @return XmlExtensionDeclaration[]
     */
    public function getExtensions()
    {
        $result = [];
        $extensions = $this->xml->getElementsByTagName('extension');
        foreach ($extensions as $extension) {
            $result[] = new XmlExtensionDeclaration($extension);
        }

        return $result;
    }
}
