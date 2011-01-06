<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\Reflection\Proxy;

use ReflectionClass;

/**
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
interface ProxyLoader
{
    /**
     * Load proxy class
     * 
     * @param ReflectionClass $class proxied class
     * @param bool $regenerate if true will try to regenerate
     * @return ReflectionClass of proxy
     */
    public function loadProxy(ReflectionClass $class, $regenerate = false);
}
?>
