<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Joomla;


class JoomlaHelper
{
    /**
     * @var JoomlaInterface
     */
    protected $joomla;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param JoomlaInterface $joomla
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setJoomla(JoomlaInterface $joomla = null)
    {
        $this->joomla = $joomla;
    }

    /**
     * @param \Twig_Environment $twig
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setTwigEnvironment(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param string $string
     * @param array  $replace
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function trans($string, $replace = [])
    {
        $text = \JText::_($string);

        foreach ($replace as $key => $value) {
            $text = str_replace($key, $value, $text);
        }

        return $text;
    }

    /**
     * @param string $item
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function menuLink($item)
    {
        $app = \JFactory::getApplication();
        $menu = $app->getMenu();
        $item = $menu->getItem($item);

        return \JRoute::_($item->link . '&Itemid=' . $item->id);
    }

    /**
     * @param string $position
     *
     * @return int
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function countModulePosition($position)
    {
        $this->joomla->getApplication();

        return count(\JModuleHelper::getModules($position));
    }

    /**
     * @param object $module
     * @param array $override
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function overrideParams($module, $override)
    {
        $params = json_decode($module->params, true);

        if (!is_array($params)) {
            $params = [];
        }

        $params = array_merge($params, $override);
        $module->params = json_encode($params);
    }

    /**
     * @param string $zone
     * @param array $parameters
     * @param array $override
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function renderModulePosition($zone, array $parameters = [], array $override = [])
    {
        $this->joomla->getApplication();
        $renderer = $this->joomla->getDocument()->loadRenderer('module');
        $modules = \JModuleHelper::getModules($zone);
        $modulesClones = [];
        foreach ($modules as $module) {
            $params = $module->params;
            $this->overrideParams($module, $override);

            if (!isset($parameters['style'])) {
                $parameters['style'] = 'none';
            }

            $content = $renderer->render($module, $parameters);

            $moduleRenderer = null;
            if (isset($module->renderer)) {
                $moduleRenderer = $module->renderer;
            }

            unset($module->renderer);
            $clone = clone $module;
            $clone->content = $content;
            $module->renderer = $clone->renderer = $moduleRenderer;
            $clone->params = json_decode($clone->params, true);
            $modulesClones[] = $clone;
            $module->params = $params;
        }

        $result = '';
        if (isset($parameters['decorator'])) {
            $result = $this->twig->render($parameters['decorator'], [
                'modules' => $modulesClones
            ]);
        } else {
            foreach ($modulesClones as $moduleClone) {
                $result .= $moduleClone->content;
            }
        }

        return $result;
    }
}