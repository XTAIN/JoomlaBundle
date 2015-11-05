<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Document;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\Asset\PackageInterface;
use XTAIN\Bundle\JoomlaBundle\Library\Joomla\Document\Html;

/**
 * Class HtmlFactory
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Document
 */
class HtmlFactory implements HtmlFactoryInterface
{
    /**
     * @var PackageInterface
     */
    protected $package;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var \Twig_Environment
     */
    protected $twigEnvironment;

    /**
     * @param PackageInterface $package
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setAssetsPackage(PackageInterface $package)
    {
        $this->package = $package;
    }

    /**
     * @param RouterInterface $router
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param \Twig_Environment $twigEnvironment
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setTwigEnvironment(\Twig_Environment $twigEnvironment)
    {
        $this->twigEnvironment = $twigEnvironment;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        Html::setAssetsPackage($this->package);
        Html::setRouter($this->router);
        Html::setTwigEnvironment($this->twigEnvironment);
    }
}
