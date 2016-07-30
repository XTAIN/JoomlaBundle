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

use Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper;
use Symfony\Component\Routing\RouterInterface;
use XTAIN\Bundle\JoomlaBundle\Library\Joomla\Document\Document;

/**
 * Class DocumentFactory
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Document
 */
class DocumentFactory implements DocumentFactoryInterface
{
    /**
     * @var AssetsHelper
     */
    protected $helper;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var \Twig_Environment
     */
    protected $twigEnvironment;

    /**
     * @param AssetsHelper $package
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setAssetsHelper(AssetsHelper $package)
    {
        $this->helper = $package;
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
        Document::setAssetsHelper($this->helper);
        Document::setRouter($this->router);
        Document::setTwigEnvironment($this->twigEnvironment);
    }
}
