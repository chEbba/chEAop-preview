<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\AOP;

/**
 * Description of Advice
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
class Advice
{
    const TYPE_BEFORE = 'before';
    const TYPE_AFTER_RETURN = 'afterReturn';
    const TYPE_AFTER_THROW = 'afterThrow';
    const TYPE_AFTER = 'after';
    const TYPE_AROUND = 'around';

    protected $type;

    protected $pointcut;

    protected $callback;
    
    public function __construct($type, Pointcut $pointcut, $callBack)
    {
        //TODO: add type checking
        $this->type = $type;
        $this->pointcut = $pointcut;
        $this->callback = $callBack;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @return Pointcut
     */
    public function getPointcut()
    {
        return $this->pointcut;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function invoke(array $args = array())
    {
        return call_user_func_array($this->callback, $args);
    }
}
