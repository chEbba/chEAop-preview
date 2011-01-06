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
interface AdviceRepository
{
    public function shiftTypedAdvice($type);

    public function getTypedAdvice($type);

    public function addAdvice(Advice $advice);

    public function addAdvices(array $advices);

    public function asArray();
}
