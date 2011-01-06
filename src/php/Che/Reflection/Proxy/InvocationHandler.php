<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\Reflection\Proxy;

use ReflectionMethod;

/**
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
interface InvocationHandler
{
    /**
     * Invoke object method
     *
     * @param object $object
     * @param ReflectionMethod $method
     * @param array $args
     */
    public function invoke($object, ReflectionMethod $method, array $args = array());
}

