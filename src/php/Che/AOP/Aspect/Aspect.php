<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Che\AOP\Aspect;

use ReflectionClass;
use InvalidArgumentException;

/**
 * Description of Aspect
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
interface Aspect
{
    public function getObject();

    public function getAdvices();
}
