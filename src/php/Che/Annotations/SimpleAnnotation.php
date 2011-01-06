<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\Annotations;

/**
 * Description of DefaultNamespace
 *
 * @author Kirill chEbba Chebunin <iam at chebba.or
 */
abstract class SimpleAnnotation extends BaseAnnotation
{
    protected $value;

    public function getValue()
    {
        return $this->value;
    }
}
