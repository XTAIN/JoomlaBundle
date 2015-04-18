<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Factory\View;

use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;
use XTAIN\Bundle\JoomlaBundle\Library\View\Legacy;

/**
 * Class LegacyFactory
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Factory\View
 */
class LegacyFactory implements DependencyFactoryInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twigEnvironment;

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
        Legacy::setTwigEnvironment($this->twigEnvironment);
    }
}
