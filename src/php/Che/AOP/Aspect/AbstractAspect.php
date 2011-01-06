<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\AOP\Aspect;

use InvalidArgumentException;

/**
 * Description of AbstractAspect
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
abstract class AbstractAspect implements Aspect
{

    protected $object;

    public function __construct($object)
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException('Object required');
        }

        $this->object = $object;
    }

    public function getObject()
    {
        return $this->object;
    }

}
