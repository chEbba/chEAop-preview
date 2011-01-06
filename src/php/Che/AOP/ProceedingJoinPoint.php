<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\AOP;

/**
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
interface ProceedingJoinPoint extends JoinPoint
{
    public function proceed();
}
