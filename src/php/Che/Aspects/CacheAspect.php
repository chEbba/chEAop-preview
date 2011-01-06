<?php
/**
 * Created by JetBrains PhpStorm.
 * User: chebba
 * Date: 12.10.10
 * Time: 23:28
 * To change this template use File | Settings | File Templates.
 */

namespace Che\Aspects;

use Zend\Cache\Backend;
use Che\AOP\ProceedingJoinPoint;

class CacheAspect
{
    /**
     * @var Backend
     */
    protected $cache;

    public function __construct(Backend $cache = null)
    {
        if ($cache !== null) {
            $this->setCache($cache);
        }
    }

    public function setCache(Backend $cache)
    {
        $this->cache = $cache;
    }

    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @Che\AOP\Aspect\Annotations\Around("@annotation(Che\Aspects\Cached)")
     */
    public function cache(ProceedingJoinPoint $joinPoint)
    {
        $cacheId = md5(serialize($joinPoint));
        
        if ($this->cache->test($cacheId)) {
            return unserialize($this->cache->load($cacheId));
        }

        $result = $joinPoint->proceed();
        $this->cache->save(serialize($result), $cacheId);
        return $result;
    }
}
