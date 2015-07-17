<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Component\Plugin\Content;

use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;

/**
 * Class TwigFactory
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Component\Plugin\Content
 */
class TwigFactory implements DependencyFactoryInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param \Twig_Environment $twig
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        Twig::setTwigEnvironment($this->twig);
    }
}