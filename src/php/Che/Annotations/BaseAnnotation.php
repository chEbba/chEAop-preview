<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\Annotations;

use BadMethodCallException;

/**
 * Description of BaseAnnotation
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
abstract class BaseAnnotation implements Annotation
{
    public function __construct(array $data)
    {
        foreach ($data as $property => $value) {
            $setter = $this->setter($property);
            if ($setter) {
                $this->{$setter}($value);
            } else {
                $this->{$property} = $value;
            }
        }
    }

    protected function setter($property)
    {
        $method = 'set' . ucfirst($property);
        if (method_exists($this, $method)) {
            return $method;
        }
        return null;
    }

    /**
     * Error handler for unknown property get
     *
     * @param string $name Unknown property name
     */
    public function __get($name)
    {
        throw new BadMethodCallException(
            sprintf("Unknown property '%s' on annotation '%s'.", $name, get_class($this))
        );
    }

    /**
     * Error handler for unknown property set
     *
     * @param string $name Unkown property name
     * @param mixed $value Property value
     */
    public function __set($name, $value)
    {
        throw new BadMethodCallException(
            sprintf("Unknown property '%s' on annotation '%s'.", $name, get_class($this))
        );
    }
}
