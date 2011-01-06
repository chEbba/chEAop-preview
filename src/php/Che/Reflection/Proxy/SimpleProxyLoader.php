<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Che\Reflection\Proxy;

use ReflectionClass;
use InvalidArgumentException;
use Che\IO\IOException;

/**
 * Description of ProxyLoader
 *
 * @author Kirill chEbba Chebunin <iam at chebba.org>
 */
class SimpleProxyLoader implements ProxyLoader
{
    /**
     * Suffix for proxy name
     * @var string
     */
    const PROXY_CLASS_PREFIX = 'CheProxy\\';

    /**
     * Directory to put proxy classes
     * @var string
     */
    protected $dir;

    /**
     * Constructor
     * @param string $dir Directory for proxies
     */
    public function __construct($dir = null)
    {
        if ($dir) {
            $this->setDir($dir);
        }
    }

    public function loadProxy(ReflectionClass $class, $regenerate = false)
    {
        //Check if class already loaded
        $proxyName = $this->proxyName($class);
        if (class_exists($proxyName, false)) {
            return new ReflectionClass($proxyName);
        }

        $proxyFilename = $this->proxyFilename($proxyName);

        //create new php file with proxy source
        if ($regenerate || !file_exists($proxyFilename)) {
            $this->writeFile($proxyFilename, $this->proxySource($class, $proxyName));
        }

        return new ReflectionClass($proxyName);
    }

    /**
     * Name of proxy class
     *
     * @param ReflectionClass $class
     * @return string
     */
    protected function proxyName(ReflectionClass $class)
    {
        return self::PROXY_CLASS_PREFIX . $class->getName();
    }

    /**
     * Name of proxy file
     *
     * @param string $proxyName name of proxy class
     * @return string
     */
    protected function proxyFilename($proxyName)
    {
        return str_replace('\\', '/', $proxyName) . '.php';
    }

    /**
     * Get directory for proxies
     *
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * Set directory for proxies
     *
     * @param string $dir directory path
     * @throws IOException
     */
    public function setDir($dir)
    {
        if (!is_dir($dir)) {
            throw new InvalidArgumentException("\"$dir\" is not a directory");
        }
        if (!is_writable($dir)) {
            throw new IOException("Directory \"$dir\" is not writable");
        }
        $this->dir = $dir;
    }

    /**
     * Write class source to filename
     *
     * @param string $filename
     * @param string $source
     */
    protected function writeFile($filename, $source)
    {
        $fileParts = explode('/', $filename);
        array_pop($fileParts);
        $subdir = implode('/', $fileParts);

        $dir = $this->dir . '/' . $subdir;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $source = '<?php' . PHP_EOL . $source;

        file_put_contents($this->dir . '/' .$filename, $source);
    }

    /**
     * Generate proxy source
     *
     * @param ReflectionClass $class
     * @param string $proxyClassName
     * @return string
     */
    protected function proxySource(ReflectionClass $class, $proxyClassName)
    {
        $generator = new ProxyGenerator($class);
        return $generator->generate($proxyClassName);
    }

}
?>
