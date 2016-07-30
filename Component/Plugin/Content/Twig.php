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

/**
 * Class Twig
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Component\Plugin\Content
 */
class Twig extends \JPlugin
{
    /**
     * @var \Twig_Environment
     */
    protected static $twig;

    /**
     * @param object $subject
     * @param array  $config
     */
    public function __construct(&$subject, array $config = [])
    {
        \XTAIN\Bundle\JoomlaBundle\Library\Loader::injectStaticDependencies(__CLASS__);

        parent::__construct($subject, $config);
    }

    /**
     * @param \Twig_Environment $twig
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setTwigEnvironment(\Twig_Environment $twig)
    {
        self::$twig = $twig;
    }

    /**
     * Plugin that adds a pagebreak into the text and truncates text at that point
     *
     * @param   string   $context  The context of the content being passed to the plugin.
     * @param   object   &$row     The article object.  Note $article->text is also available
     * @param   mixed    &$params  The article params
     * @param   integer  $page     The 'page' number
     *
     * @return  mixed  Always returns void or true
     *
     * @since   1.6
     */
    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {
        $canProceed = $context == 'com_content.article';

        if (!$canProceed) {
            return;
        }

        if (is_object($row)) {
            return $this->render($row->text, $params);
        }

        return $this->render($row, $params);
    }

    /**
     * @param string $text
     *
     * @return bool
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function render(&$text)
    {
        $loader = self::$twig->getLoader();
        $chainLoader = new \Twig_Loader_Chain([
            new \Twig_Loader_String()
        ]);
        self::$twig->setLoader($chainLoader);
        $text = self::$twig->render($text);
        self::$twig->setLoader($loader);

        return true;

    }
}