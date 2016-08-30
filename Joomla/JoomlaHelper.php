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
     * @param string|object $module
     * @param array $params
     * @param array $settings
     *
     * @return string
     */
    public function renderModule($module, $params = array(), $settings = array())
    {
        $style = null;

        if (is_object($module)) {
            $object = clone $module;
        } else {
            $object = new \stdClass();
        }
        if (!isset($object->published)) {
            $object->published = 1;
        }
        if (!is_object($module)) {
            $object->module = $module;
        }
        if (!isset($object->position)) {
            $object->position = '';
        }
        if (!isset($object->content)) {
            $object->content = '';
        }
        if (!isset($object->menuid)) {
            $object->menuid = '0';
        }
        if (!is_object($module)) {
            $object->name = preg_replace('/^mod_/', '', $module);
        }
        if (!isset($object->style)) {
            $object->style = null;
        }
        if (!isset($object->title)) {
            $object->title = '';
        }
        if (!isset($object->showtitle)) {
            $object->showtitle = '0';
        }

        if (isset($settings['title'])) {
            $object->title = $settings['title'];
            $object->showtitle = '1';
        }

        if (isset($settings['style'])) {
            $style = $object->style = $settings['style'];
        }

        $params = array_merge(array(
            'layout' => 'default',
            'moduleclass_sfx' => '',
            'cache' => 0,
            'cache_time' => 900,
            'cachemode' => 'itemid',
            'module_tag' => 'div',
            'bootstrap_size' => 0,
            'header_tag' => 'h2',
            'header_class' => '',
            'style' => '0'
        ), $params);

        $object->params = json_encode($params);

        $this->renderModuleObject($object, array(
            'style' => $style
        ));

        return $object->content;
    }

    /**
     * @param object $module
     * @param array $parameters
     * @param array $override
     *
     * @return object
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function renderModuleObject($module, array $parameters = [], array $override = [])
    {
        $this->joomla->getApplication();
        $renderer = $this->joomla->getDocument()->loadRenderer('module');

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
        $module->params = $params;

        return $clone;
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

        $modules = \JModuleHelper::getModules($zone);
        $modulesClones = [];
        foreach ($modules as $module) {
            $modulesClones[] = $this->renderModuleObject($module, $parameters, $override);
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

    /**
     * @param integer $id
     *
     * @return object
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getModuleById($id)
    {
        $modules = \JModuleHelper::getModules(null);

        $total = count($modules);

        for ($i = 0; $i < $total; $i++)
        {
            if ($modules[$i]->id == $id)
            {
                return $modules[$i];
            }
        }

        return null;
    }
}