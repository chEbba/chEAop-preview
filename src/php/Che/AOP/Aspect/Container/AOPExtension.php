<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\AOP\Aspect\Container;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Che\Reflection\Proxy\ProxyClass;
use Che\Reflection\Proxy\SimpleProxyLoader;

/**
 * Description of AOPExtension
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
class AOPExtension extends Extension
{

    const KEY_REPOSITORY = 'che.aop._repository';
    const KEY_HANDLER = 'che.aop._handler';

    protected static $KEY_ASPECT_PREFIX = 'che.aop._aspect.';

    /**
     *
     *
     * @param array            $config    An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    protected function configLoad($config, ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::KEY_HANDLER)) {
            $container->setDefinition(self::KEY_HANDLER, $this->createHandlerDefinition());
        }

        $repositoryDefinition = $this->getRepositoryDefinition($container);

        if (isset($config['proxyDir'])) {
            ProxyClass::setLoader(new SimpleProxyLoader($config['proxyDir']));
        }

        if (!isset($config['aspect'])) {
            return;
        }

        //if we have only 1 aspect, it will be directly aspect params
        if (!isset($config['aspect'][0])) {
            $config['aspect'] = array($config['aspect']);
        }

        foreach($config['aspect'] as $aspectConfig) {
            $repositoryDefinition->addMethodCall('addAspect', array(
                $this->getAspectReference($container, $aspectConfig)
            ));
        }

    }

    /**
     *
     * @param ContainerBuilder $container
     * @return Definition
     */
    protected function getRepositoryDefinition(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::KEY_REPOSITORY)) {
            $container->setDefinition(
                    self::KEY_REPOSITORY,
                    $this->createRepositoryDefinition());
        }

        return $container->getDefinition(self::KEY_REPOSITORY);
    }

    protected function createRepositoryDefinition()
    {
        return new Definition('Che\\AOP\\Aspect\\AspectRepository');
    }

    protected function createHandlerDefinition()
    {
        return new Definition('Che\\AOP\\AOPHandler', array(
            new Reference(self::KEY_REPOSITORY)
        ));
    }

    protected function getAspectReference(ContainerBuilder $container, $aspectConfig)
    {
        $serviceKey = self::$KEY_ASPECT_PREFIX . $aspectConfig['ref'];
        if (!$container->hasDefinition($serviceKey)) {
            $container->setDefinition(
                $serviceKey,
                new Definition('Che\\AOP\\Aspect\\AnnotatedAspect', array(
                    new Reference($aspectConfig['ref']),
                    new Reference('annotation.reader')
                ))
            );
        }

        return new Reference($serviceKey);
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/Resources/config/schema';
    }

    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/aop';
    }

    public function getAlias()
    {
        return 'aop';
    }
}
