<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\View;

use XTAIN\Bundle\JoomlaBundle\Library\Joomla\Document\Html;

/**
 * Class Legacy
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\View
 */
class Legacy extends \JProxy_JViewLegacy
{
    /**
     * @var \Twig_Environment
     */
    protected static $twigEnvironment;

    /**
     * @param \Twig_Environment $twigEnvironment
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setTwigEnvironment(\Twig_Environment $twigEnvironment)
    {
        self::$twigEnvironment = $twigEnvironment;
    }

    protected function getTwigEnvironment()
    {
        return self::$twigEnvironment;
    }

    /**
     * @param string $path
     * @param null   $template
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function findTwigFile($path, $template = null)
    {
        if ($template == null) {
            $template = $this->template;
        }

        return '::' . Html::TWIG_TEMPLATE_PATH . '/' . $template . '/' . $path;
    }

    protected function getTwigContext($context)
    {
        return array_merge(
            $context,
            [
                'this' => $this
            ]
        );
    }

    /**
     * @param string $file
     * @param array  $context
     * @param null   $template
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function renderTwig($file, $context = [], $template = null)
    {
        $twig = $this->getTwigEnvironment();

        $template = $twig->loadTemplate($this->findTwigFile($file, $template));

        return $template->render($this->getTwigContext($context));
    }
}
