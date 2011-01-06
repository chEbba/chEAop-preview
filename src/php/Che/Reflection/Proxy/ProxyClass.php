<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\Reflection\Proxy;

use ReflectionClass;
use ReflectionObject;

/**
 * Description of ProxyClass
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
class ProxyClass implements \Reflector
{
    /**
     * Class to proxy
     * @var ReflectionClass
     */
    protected $proxiedClass;

    /**
     * Proxy loader
     * @var ProxyLoader
     */
    protected static $loader;

    /**
     * Constructor
     * 
     * @param ReflectionClass $proxiedClass
     */
    public function __construct(ReflectionClass $proxiedClass)
    {
        $this->proxiedClass = $proxiedClass;
    }

    /**
     * Get proxied class
     *
     * @return ReflectionClass
     */
    public function getProxiedClass()
    {
        return $this->proxiedClass;
    }

    /**
     * Get proxy class
     *
     * @param array $interfaces
     * @return ReflectionClass
     */
    public function getClass()
    {
        return self::getLoader()->loadProxy($this->proxiedClass);
    }

    /**
     * Create new instance of proxy
     * 
     * @param InvocationHandler $handler
     * @param array $args
     * @return Proxy
     */
    public function newProxyInstance(InvocationHandler $handler, array $args = array())
    {
        $class = $this->getClass();
        $instance = count($args) ? $class->newInstanceArgs($args) : $class->newInstance();
        $instance->setInvocationHandler($handler);
        return $instance;
    }

    public static function createProxyFromObject($object, $handler)
    {
        $ref = new ReflectionObject($object);
        $proxy = new static($ref);
        /* @var $proxyClass ReflectionClass */
        $proxyClass = $proxy->getClass();

        $objectString = serialize($object);
        $classesDiff = strlen($proxyClass->getName()) - strlen($ref->getName());
        $pattern = '/([0-9]+)\:"(' . preg_quote($ref->getName()) . ')"/';

        $objectString = preg_replace_callback(
            $pattern,
            function(array $matches) use (&$classesDiff, &$proxyClass) {
                return $matches[1] + $classesDiff . ":\"{$proxyClass->getName()}\"" ;

            },
            $objectString
        );
        
        $proxyObject = unserialize($objectString);

        $proxyObject->setInvocationHandler($handler);
        return $proxyObject;
    }

    /**
     * Get proxy loader
     * 
     * @return ProxyLoader
     */
    public static function getLoader()
    {
        return self::$loader;
    }

    /**
     * Set proxy loader
     * 
     * @param ProxyLoader $loader
     */
    public static function setLoader(ProxyLoader $loader)
    {
        self::$loader = $loader;
    }

    public function __toString()
    {
        return "ProxyClass of [{$this->proxiedClass->getName()}]";
    }

    public static function export()
    {
        //FIXME: don't know what need to be here, may be just export of proxy class
        return null;
    }
}

