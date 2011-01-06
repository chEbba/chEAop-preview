<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\Aspects;

use Zend\Log\Logger;
use Exception;
use Che\AOP\JoinPoint;

/**
 * Description of LogAspect
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
class LogAspect
{
    /**
     * @var Logger
     */
    protected $logger;

    public function __construct(Logger $logger = null)
    {
        if ($logger !== null) {
            $this->setLogger($logger);
        }
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Che\AOP\Aspect\Annotations\After("@annotation(Che\Aspects\Log)")
     */
    public function log($returned, $value, JoinPoint $joinPoint)
    {
        $returned ? 
            $this->logResult($value, $joinPoint) :
            $this->logException($value, $joinPoint);
    }

    protected function logResult($result, JoinPoint $joinPoint)
    {
        $method = $joinPoint->getMethod();
        $this->logger->log(
            "Method {$method->getName()} executed correctly",
            Logger::INFO,
            array(
                'method' => $method,
                'args' => $joinPoint->getArgs(),
                'return' => $result
            )
       );
    }

    protected function logException(Exception $e, JoinPoint $joinPoint)
    {
        $method = $joinPoint->getMethod();
        $this->logger->log(
            sprintf(
                "Method %s threw an Exception '%s' with message '%s'",
                $method->getName(),
                get_class($e),
                $e->getMessage()
            ),
            Logger::ERR,
            array(
                'method' => $method,
                'args' => $joinPoint->getArgs(),
                'exception' => $e
            )
       );
    }

}
