<?php
namespace CheProxy\NS;

class TestAOP extends \NS\TestAOP implements \Che\Reflection\Proxy\Proxy
{

    private $invocationHandler = null;

    public function cacheMe()
    {
        return $this->invocationHandler->invoke(
                    $this,
                    new \ReflectionMethod(get_parent_class(__CLASS__), __FUNCTION__),
                    func_get_args());
    }

    public function logMe(\DateTime $date)
    {
        return $this->invocationHandler->invoke(
                    $this,
                    new \ReflectionMethod(get_parent_class(__CLASS__), __FUNCTION__),
                    func_get_args());
    }

    public function getInvocationHandler()
    {
        return $this->invocationHandler;
    }

    public function setInvocationHandler(\Che\Reflection\Proxy\InvocationHandler $invocationHandler)
    {
        $this->invocationHandler = $invocationHandler;return $this;
    }


}
