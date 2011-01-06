<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\AOP;

/**
 * Description of JoinPoint
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
interface JoinPoint
{
    public function getObject();

    public function getArgs();

    /**
     * @return \ReflectionMethod
     */
    public function getMethod();
}
