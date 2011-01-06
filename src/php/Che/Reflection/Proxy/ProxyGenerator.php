<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

namespace Che\Reflection\Proxy;

use Zend\CodeGenerator\Php\PhpClass,
        Zend\CodeGenerator\Php\PhpMethod,
        Zend\CodeGenerator\Php\PhpParameter,
        Zend\CodeGenerator\Php\PhpProperty;
use ReflectionMethod,
        ReflectionClass;
use InvalidArgumentException;

/**
 * Description of ProxyGenerator
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
class ProxyGenerator
{
    /**
     * Class to proxy
     * @var ReflectionClass
     */
    private $class;

    /**
     * Available className regex
     * @var string
     */
    private static $classNameRegex = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\\\\]*$/';

    /**
     * Constructor
     * 
     * @param ReflectionClass $class
     */
    public function  __construct(ReflectionClass $class) {
        if ($class->isFinal()) {
            throw new InvalidArgumentException('Can not proxy the final class');
        }
        $this->class = $class;
    }

    /**
     * Get proxied class
     * 
     * @return ReflectionClass
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * Generate a proxy source
     * 
     * @param string $proxyClassName Name of proxy class
     * @return string Source
     */
    public function generate($proxyClassName)
    {
        //check the proxy class name
        if (!preg_match(self::$classNameRegex, $proxyClassName)) {
            throw new InvalidArgumentException(sprintf(
                    'Wrong class name "%s", should match pattern: "%s"',
                    $proxyClassName,
                    self::$classNameRegex));
        }

        //get name parts
        $nameParts = explode('\\', $proxyClassName);
        $proxyClassName = array_pop($nameParts);
        $proxyNamespace = implode('\\', $nameParts);

        //set main options of proxy
        $generator = new PhpClass();
        $generator->setName($proxyClassName)
                ->setProperties($this->proxySupportProperties()) //private options for handler
                ->setMethods(array_merge(
                        $this->proxyMethods(),
                        $this->proxySupportMethods()));

        //set interfaces and parent class
        $interfaces = array('\\Che\\Reflection\\Proxy\\Proxy');
        if ($this->class->isInterface()) {
            $interfaces[] = $this->class->getName();
        } else {
            $generator->setExtendedClass('\\' . $this->class->getName());
        }
        $generator->setImplementedInterfaces($interfaces);

        //Add namespace to proxy
        $classSource = $generator->generate();
        if ($proxyNamespace) {
            $classSource = "namespace $proxyNamespace;"
                . PHP_EOL . PHP_EOL . $classSource;
        }

        return $classSource;
    }

    /**
     * Check if we can proxy this method
     * 
     * @param ReflectionMethod $method
     * @return bool
     */
    protected function isMethodOverwriteable(ReflectionMethod $method)
    {
        return (!$method->isPrivate() 
                && !$method->isFinal()
                && !$method->isStatic());
    }

    /**
     * Get methods code generators
     * 
     * @return array of PhpMethod
     */
    protected function proxyMethods()
    {
        $methods = array();
        foreach ($this->class->getMethods() as $method) {
            if ($this->isMethodOverwriteable($method)) {
                $methods[] = $this->proxyMethod($method);
            }
        }

        return $methods;
    }

    /**
     * Get code generator for method
     * 
     * @param ReflectionMethod $method
     * @return PhpMethod
     */
    protected function proxyMethod(ReflectionMethod $method)
    {
        $generator = new PhpMethod();
        $generator->setName($method->getName())
                ->setVisibility($method->isPublic() ? 
                        PhpMethod::VISIBILITY_PUBLIC :
                        PhpMethod::VISIBILITY_PROTECTED);

        //Set method parameters
        /* @var $parameter \ReflectionParameter */
        foreach ($method->getParameters() as $parameter) {
            $paramGenerator = new PhpParameter();
            $paramGenerator->setName($parameter->getName())
                    ->setPassedByReference($parameter->isPassedByReference())
                    ->setPosition($parameter->getPosition());
            if ($parameter->isOptional()) {
                $paramGenerator->setDefaultValue($parameter->getDefaultValue());
            }

            if ($parameter->isArray()) {
                $paramGenerator->setType('array');
            } else {
                $typeClass = $parameter->getClass();
                if($typeClass !== null) {
                    $paramGenerator->setType('\\' . $typeClass->getName());
                }
            }
            $generator->setParameter($paramGenerator);
        }

        $generator->setBody($this->proxyMethodSource());

        return $generator;
    }

    /**
     * Get source for proxy method
     * 
     * @return string
     */
    protected function proxyMethodSource()
    {
        return 'return $this->invocationHandler->invoke(
            $this,
            new \ReflectionMethod(get_parent_class(__CLASS__), __FUNCTION__),
            func_get_args());';
    }

    /**
     * Get options of property generators for Proxy interface
     * 
     * @return array
     */
    protected function proxySupportProperties()
    {
        return array(
            array(
                'name' => 'invocationHandler',
                'visibility' => 'private'
            )
        );
    }

    /**
     * Get options of method generators for Proxy interface
     * 
     * @return array
     */
    protected function proxySupportMethods()
    {
        return array(
            array(
                'name' => 'getInvocationHandler',
                'body' => 'return $this->invocationHandler;'
            ),
            array(
                'name' => 'setInvocationHandler',
                'parameter' => array(
                    'name' => 'invocationHandler',
                    'type' => '\\Che\\Reflection\\Proxy\\InvocationHandler'
                ),
                'body' => '$this->invocationHandler = $invocationHandler;return $this;'
            )
        );
    }
}
