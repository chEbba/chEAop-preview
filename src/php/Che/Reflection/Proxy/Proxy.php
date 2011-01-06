<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\Reflection\Proxy;

/**
 * Description of Proxy
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
interface Proxy {
    /**
     * Get invocation handler
     *
     * @return InvocationHandler
     */
    public function getInvocationHandler();

    /**
     * Set invocation handler
     *
     * @param InvocationHandler $invocationHandler
     */
    public function setInvocationHandler(InvocationHandler $invocationHandler);

}

