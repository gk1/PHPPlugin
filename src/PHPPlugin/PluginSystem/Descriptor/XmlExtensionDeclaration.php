<?php
namespace PHPPlugin\PluginSystem\Descriptor;

use PHPPlugin\PluginSystem\ExtensionDeclarationInterface;

/**
 * Implementation of an extension descriptor based on XML files
 * @package PHPPlugin\PluginSystem\Descriptor
 */
class XmlExtensionDeclaration implements ExtensionDeclarationInterface
{
    /**
     * Xml Declaration.
     *
     * @var \DOMElement
     */
    private $dom;

    public function __construct(\DOMElement $element)
    {
        $this->dom = $element;
    }

    /**
     * Returns the extension type.
     *
     * @return string
     */
    public function getType()
    {
        return (string) $this->dom->getAttribute('type');
    }

    /**
     * Returns the extension implementation className.
     *
     * @return string
     */
    public function getClassName()
    {
        return (string) $this->dom->getAttribute('className');
    }

    /**
     * Returns the other extension declaration attributes.
     *
     * @return string[]
     */
    public function getAttributes()
    {
        $result = [];
        $attributes = $this->dom->attributes;
        for ($i = 0; $i < $attributes->length; ++$i) {
            $result[$attributes->item($i)->nodeName] = $attributes->item($i)->nodeValue;
        }

        return $result;
    }
}
