<?php
namespace NS;

use Che\AOP\Aspect\Container\AOPExtension;
use Che\AOP\Aspect\Container\AspectedContainer;

$libDir = __DIR__ . '/../lib';

require_once "$libDir/Zend/Loader/StandardAutoloader.php";

$autoloader = new \Zend\Loader\StandardAutoloader(array(
    'namespaces' => array(
        'Zend' => "$libDir/Zend",
        'Doctrine' => "$libDir/Doctrine",
        'Symfony' => "$libDir/Symfony",
        'Che' => __DIR__ . '/../src/php/Che',
        'CheProxy' => __DIR__ . '/proxy/CheProxy'
    )
));
$autoloader->register();

class TestAOP
{

    /**
     * @aop:Cached
     */
    public function cacheMe()
    {
        echo __METHOD__ . PHP_EOL;
        foreach(range(1, 100) as $i) {
            $a = new \ReflectionClass(get_class());
        }
        
        return 'Cached method' . PHP_EOL;
    }

    /**
     * @aop:Log
     */
    public function logMe(\DateTime $date)
    {
        echo __METHOD__ . PHP_EOL;
        return 'success';
    }
}

AspectedContainer::registerExtension(new AOPExtension());
$container = new AspectedContainer();

$loader = new \Symfony\Component\DependencyInjection\Loader\XmlFileLoader($container);
$loader->load(__DIR__.'/config.xml');

$container->freeze();

/* @var $test TestAOP */
$test = $container->get('test.aop');

echo $test->cacheMe();
$test->logMe(new \DateTime());
var_dump($container->get('logger.writer')->events);

