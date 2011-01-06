<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\AOP;

use ReflectionMethod;

/**
 * Description of Pointcut
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
interface Pointcut
{
    public function isMatched(ReflectionMethod $method);
}
