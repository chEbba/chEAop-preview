<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\Annotations;

use Zend\Server\Reflection\ReflectionClass;
use Zend\Reflection\ReflectionProperty;
use Zend\Reflection\ReflectionMethod;

/**
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
interface AnnotationReader
{
    public function getClassAnnotations(ReflectionClass $class);
    
    public function getClassAnnotation(ReflectionClass $class, $annotation);
    
    public function getPropertyAnnotations(ReflectionProperty $property);
    
    public function getPropertyAnnotation(ReflectionProperty $property, $annotation);
    
    public function getMethodAnnotations(ReflectionMethod $method);
    
    public function getMethodAnnotation(ReflectionMethod $method, $annotation);
}
