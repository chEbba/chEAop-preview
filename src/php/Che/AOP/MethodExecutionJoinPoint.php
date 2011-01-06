<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\AOP;

use InvalidArgumentException;

/**
 * Description of MethodExecutionJoinPoint
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
class MethodExecutionJoinPoint extends SimpleJoinPoint
        implements ProceedingJoinPoint
{
    /**
     * @var array
     */
    protected $aroundAdvices = array();

    public function setAdvices(array $advices)
    {
        foreach ($advices as $advice) {
            $this->addAdvice($advice);
        }

        return $this;
    }

    public function addAdvice(Advice $advice)
    {
        if ($advice->getType() != Advice::TYPE_AROUND) {
            throw new InvalidArgumentException(
                    "Advice has the wrong type '{$advice->getType()}' - Advice::TYPE_AROUND required");
        }

        array_push($this->aroundAdvices, $advice);

        return $this;
    }

    public function proceed()
    {
        //proceed around advices, each advice will continue or stop an execution
        while ($advice = array_pop($this->aroundAdvices)) {
            return $advice->invoke(array($this));
        }

        //method can be protected
        $this->method->setAccessible(true);
        return $this->method->invokeArgs($this->object, $this->args);
    }

    /**
     *
     * @param JoinPoint $joinPoint
     * @param array $advices
     * @return MethodExecutionJoinPoint
     */
    public static function fromJointPoint(JoinPoint $joinPoint, array $advices = array())
    {
        $proceedingJoinPoint = new static($joinPoint->getObject(),
                $joinPoint->getMethod(), $joinPoint->getArgs());

        $proceedingJoinPoint->setAdvices($advices);

        return $proceedingJoinPoint;
    }
}
