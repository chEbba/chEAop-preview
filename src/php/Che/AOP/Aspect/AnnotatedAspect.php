<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\AOP\Aspect;

use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionObject;
use Che\AOP\Advice;

/**
 * Description of AnnotatedAspect
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
class AnnotatedAspect extends AbstractAspect
{
    protected $reader;

    protected $advices = null;

    public function __construct($object, AnnotationReader $reader = null)
    {
        $this->reader = $reader ?: new AnnotationReader();
        parent::__construct($object);
    }

    public function getAdvices()
    {
        if ($this->advices === null) {
            $this->collectAdvices();
        }

        return $this->advices;
    }

    protected function collectAdvices()
    {
        $this->advices = array();
        $ref = new ReflectionObject($this->object);
        /* @var $method \ReflectionMethod */
        foreach ($ref->getMethods() as $method) {
            foreach (self::getAnnotations() as $type => $annotationClass) {
                $annotation = $this->reader->getMethodAnnotation($method, $annotationClass);
                if ($annotation) {
                    $pointcut = new AspectPointcut($annotation->getValue(), $this->reader);
                    $advice = new Advice($type, $pointcut, array(
                        $this->object,
                        $method->getName()
                    ));
                    $this->advices[] = $advice;
                }
            }
        }
    }

    public function getReader()
    {
        return $this->reader;
    }

    public function setReader($reader)
    {
        $this->reader = $reader;
    }

    protected static function getAnnotations()
    {
        return array(
            Advice::TYPE_AROUND => __NAMESPACE__ . '\\Annotations\\Around',
            Advice::TYPE_AFTER  => __NAMESPACE__ . '\\Annotations\\After'
        );
    }
}
