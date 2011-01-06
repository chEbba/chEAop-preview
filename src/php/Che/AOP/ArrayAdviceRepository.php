<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\AOP;

use Che\Util\Enum\Constant;
use InvalidArgumentException;

/**
 * Description of AdviceRepository
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
class ArrayAdviceRepository implements AdviceRepository
{
    protected $repository = array();

    public function  __construct(array $advices = array())
    {
        $types = $this->adviceTypes();
        $this->repository = array_fill_keys($types, array());
        $this->addAdvices($advices);
    }

    protected static function adviceTypes()
    {
        return Constant::classConstants(__NAMESPACE__ . '\\Advice', 'type');
    }

    protected static function isValidType($type)
    {
        return Constant::isConstantValid(__NAMESPACE__ . '\\Advice', $type, 'type');
    }

    public function addAdvice(Advice $advice)
    {
        array_push($this->repository[$advice->getType()], $advice);
        return $this;
    }

    public function addAdvices(array $advices)
    {
        foreach ($advices as $advice) {
            $this->addAdvice($advice);
        }

        return $this;
    }

    public function getTypedAdvice($type)
    {
        return isset($this->repository[$type][0]) ? $this->repository[$type][0] : null;
    }

    public function shiftTypedAdvice($type)
    {
        return array_shift($this->repository[$type]);
    }

    public function asArray()
    {
        return $this->repository;
    }
}
