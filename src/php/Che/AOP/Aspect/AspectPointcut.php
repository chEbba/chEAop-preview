<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\AOP\Aspect;

use Che\AOP\Pointcut;
use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionMethod;

/**
 * Description of AspectPointcut
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
class AspectPointcut implements Pointcut
{
    protected $expression;
    /**
     * @var AnnotationReader
     */
    protected $reader;

    /**
     *
     * @param string $expression
     */
    public function __construct($expression, AnnotationReader $reader = null)
    {
        $this->expression = $expression;
        $this->reader = $reader ?: new AnnotationReader();
    }
    
    public function isMatched(ReflectionMethod $method)
    {
        $matches = array();
        if (preg_match('/^\@annotation\(([a-zA-Z_0-9\\\]+)\)$/', $this->expression, $matches)) {
            $annotationClass = $matches[1];
            return (bool) $this->reader->getMethodAnnotation($method, $annotationClass);
        }

        return false;
    }
}
