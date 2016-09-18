<?php
namespace PHPPlugin\PluginSystem\ServiceContainer;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Simple service container `mock` capable of creating new objects
 * @package PHPPlugin\PluginSystem\Locator
 */
class MockServiceContainer implements ContainerInterface
{

    public function hasParameter($name)
    {
        // TODO: Implement hasParameter() method.
    }

    public function getParameter($name)
    {
        // TODO: Implement getParameter() method.
    }

    public function setParameter($name, $value)
    {
        // TODO: Implement setParameter() method.
    }

    public function initialized($id)
    {
        // TODO: Implement initialized() method.
    }

    public function has($id)
    {
        // TODO: Implement has() method.
    }

    public function set($id, $service)
    {
        // TODO: Implement set() method.
    }

    /**
     * Returns new instance of the $className class.
     *
     * @param string $id
     * @param int $invalidBehavior
     *
     * @return mixed instance of $className class
     */
    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        return new $id();
    }
}
