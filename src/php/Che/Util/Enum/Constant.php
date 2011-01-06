<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\Util\Enum;

use Zend\Validator\Validator;
use ReflectionClass;

/**
 * Description of Constant
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
class Constant implements Validator
{
    protected $class;

    protected $type;

    protected static $MESSAGE_TEMPLATE = "Value should be one of '%s' class constants (%s)";

    protected $message;

    public function  __construct($class, $type = null)
    {
        $this->class = $class;
        $this->type = $type;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getType()
    {
        return $this->type;
    }

    public function isValid($value)
    {
        $constants = self::classConstants($this->class, $this->type);
        $valid = in_array($value, $constants);

        if (!$valid) {
            $this->message = sprintf(self::$MESSAGE_TEMPLATE,
                    $this->class,
                    implode(', ', array_keys($constants)));
        }

        return $valid;
    }

    public function getMessages()
    {
        return array($this->message);
    }

    public static function isConstantValid($class, $constName, $type = null)
    {
        $validator = new self($class, $type);
        return $validator->isValid($constName);
    }

    public static function classConstants($class, $type = null)
    {
        $ref = new ReflectionClass($class);
        $constants = $ref->getConstants();
        if (!$type) {
            return $constants;
        }

        $type = strtoupper($type);

        $typeConstants = array();
        foreach ($constants as $name => $value)
        {
            if (strpos($name, $type . '_') === 0) {
                $typeConstants[$name] = $value;
            }
        }

        return $typeConstants;
    }
}
