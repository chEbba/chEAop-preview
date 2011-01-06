<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\AOP\Aspect;

use Che\AOP\ArrayAdviceRepository;

/**
 * Description of AspectRepository
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
class AspectRepository extends ArrayAdviceRepository
{
    public function addAspect(Aspect $aspect)
    {
        $this->addAdvices($aspect->getAdvices());
        return $this;
    }

    /**
     *
     * @param array $aspects array of Aspect
     */
    public function addAspects(array $aspects)
    {
        foreach ($aspects as $aspect) {
            $this->addAspect($aspect);
        }
        return $this;
    }
}
