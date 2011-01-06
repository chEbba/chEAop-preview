<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\AOP;

use Che\Reflection\Proxy\InvocationHandler;
use ReflectionMethod;
use InvalidArgumentException;
use Che\Util\Enum\Constant;
use Exception;

/**
 * Description of ProxyHandler
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
class AOPHandler implements InvocationHandler
{

    protected $adviceRepository;

    public function  __construct(AdviceRepository $adviceRepository = null)
    {
        $adviceRepository = $adviceRepository ?: new ArrayAdviceRepository();
        $this->setAdviceRepository($adviceRepository);
    }

    public function setAdviceRepository(AdviceRepository $adviceRepository)
    {
        $this->adviceRepository = $adviceRepository;
        return $this;
    }

    public function getAdviceRepository()
    {
        return $this->adviceRepository;
    }

    public function invoke($object, ReflectionMethod $method, array $args = array())
    {
        $joinPoint = new SimpleJoinPoint($object, $method, $args);
        $adviceRep = $this->methodAdvices($method);

        $advices = $adviceRep->asArray();

        $proceedingJoinPoint = MethodExecutionJoinPoint::fromJointPoint($joinPoint,
                $advices[Advice::TYPE_AROUND]);
        
        //BEFORE
        $this->callTypedAdvices($adviceRep, Advice::TYPE_BEFORE, array($joinPoint));

        $exception = null;
        $returnValue = null;
        $returned = true;

        try {
            //AROUND and method execution
            $returnValue = $proceedingJoinPoint->proceed();
        } catch (Exception $e) {
            $returned = false;
            $exception = $e;
        }

        //AFTER_RETURN
        if ($returned) {
            $this->callTypedAdvices($adviceRep, Advice::TYPE_AFTER_RETURN, array(
                $returnValue,
                $joinPoint
            ));
        } else { //AFTER_THROW
            $this->callTypedAdvices($adviceRep, Advice::TYPE_AFTER_THROW, array(
                $exception,
                $joinPoint
            ));
        }

        //AFTER
        $this->callTypedAdvices($adviceRep, Advice::TYPE_AFTER, array(
            $returned,
            $returned ? $returnValue : $exception,
            $joinPoint
        ));

        if (!$returned) {
            throw new $exception;
        }

        return $returnValue;
    }

    protected function callTypedAdvices(AdviceRepository $adviceRepository,
            $type, array $args = array())
    {
        /* @var $advice Advice */
        while($advice = $adviceRepository->shiftTypedAdvice($type)) {
            $advice->invoke($args);
        }
    }

    protected function methodAdvices(ReflectionMethod $method)
    {
        $advices = array();

        array_walk_recursive(
            $this->adviceRepository->asArray(),
            function($advice) use (&$advices, &$method) 
            {
                if ($advice->getPointcut()->isMatched($method)) {
                    $advices[] = $advice;
                }
            }
        );

        return new ArrayAdviceRepository($advices);

    }

}
?>
