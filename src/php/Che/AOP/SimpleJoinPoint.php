<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\AOP;

use ReflectionMethod;

/**
 * Description of SimpleJoinPoint
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
class SimpleJoinPoint implements JoinPoint
{
    protected $object;

    /**
     *
     * @var ReflectionMethod
     */
    protected $method;

    protected $args = array();

    public function __construct($object, ReflectionMethod $method, array $args = array())
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException('Object should be an object!');
        }

        $this->object = $object;
        $this->method = $method;
        $this->args   = $args;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function getArgs()
    {
        return $this->args;
    }

    public function getMethod()
    {
        return $this->method;
    }

}
