<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\AOP\Aspect\Container;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Che\Reflection\Proxy\ProxyClass;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Description of AspectedContainer
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
class AspectedContainer extends ContainerBuilder
{
    
    public function __construct(ParameterBagInterface $parameterBag = null)
    {
        parent::__construct($parameterBag);
        $extension = new AOPExtension();
        if (!self::hasExtension($extension->getAlias())) {
            self::registerExtension($extension);
        }

    }

    protected function createService(Definition $definition, $id)
    {
        
        $service = parent::createService($definition, $id);

        if ($id == AOPExtension::KEY_HANDLER
                || isset($this->loading[AOPExtension::KEY_HANDLER])) {
            return $service;
        }

        $handler = $this->getAOPHandler();

        if (!$handler) {
            return $service;
        }

        $serviceProxy = ProxyClass::createProxyFromObject($service, $handler);
        if ($definition->isShared()) {
            $this->resources[$id] = $serviceProxy;
        }

        return $serviceProxy;
    }

    protected function getAOPHandler()
    {
        return $this->get(AOPExtension::KEY_HANDLER, ContainerInterface::NULL_ON_INVALID_REFERENCE);
    }
}
