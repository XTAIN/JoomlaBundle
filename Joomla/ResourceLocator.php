<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Joomla;

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ResourceLocator
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Installation
 */
class ResourceLocator
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * ResourceLocator constructor.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param string $path
     *
     * @return array|string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function locate($path)
    {
        if (substr($path, 0, 1) == '@') {
            $path = $this->kernel->locateResource($path);
        } else {
            $path = $this->kernel->getRootDir() . DIRECTORY_SEPARATOR . $path;
        }

        return $path;
    }

    /**
     * @param string  $path
     * @param boolean $relative
     *
     * @return array|string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function rewrite($path, $relative = false)
    {
        $root = '';

        if (!$relative) {
            $root = $this->kernel->getRootDir() . DIRECTORY_SEPARATOR;
        }

        if (strpos($path, 'templates/') === 0) {
            return $root . 'Resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'joomla' . DIRECTORY_SEPARATOR . $path;
        }

        return $root . 'Resources' . DIRECTORY_SEPARATOR . 'joomla' . DIRECTORY_SEPARATOR . $path;
    }
}