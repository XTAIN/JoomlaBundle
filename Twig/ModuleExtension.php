<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Twig;

use XTAIN\Bundle\JoomlaBundle\Joomla\JoomlaAwareInterface;
use XTAIN\Bundle\JoomlaBundle\Joomla\JoomlaInterface;

/**
 * Class ModuleExtension
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Twig
 */
class ModuleExtension extends \Twig_Extension implements JoomlaAwareInterface
{
    /**
     * @var JoomlaInterface
     */
    protected $joomla;

    /**
     * @param JoomlaInterface $joomla
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setJoomla(JoomlaInterface $joomla = null)
    {
        $this->joomla = $joomla;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('joomla_module_position_count', [$this, 'countModulePosition']),
            new \Twig_SimpleFunction('joomla_message', [$this, 'message'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('joomla_component', [$this, 'component'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('joomla_head', [$this, 'head'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('joomla_module_position', [$this, 'renderModulePosition'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('joomla_trans', [$this, 'trans'])
        ];
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('joomla_trans', [$this, 'trans'])
        );
    }

    public function head()
    {
        return '<jdoc:include type="head" />';
    }

    public function component()
    {
        return '<jdoc:include type="component" />';
    }

    public function message()
    {
        return '<jdoc:include type="message" />';
    }

    public function countModulePosition($zone)
    {
        $this->joomla->getApplication();

        return count(\JModuleHelper::getModules($zone));
    }

    protected function overrideParams($module, $override)
    {
        $params = json_decode($module->params, true);

        if (!is_array($params)) {
            $params = [];
        }

        $params = array_merge($params, $override);
        $module->params = json_encode($params);
    }

    public function renderModulePosition(\Twig_Environment $twig, $zone, array $parameters = [], array $override = [])
    {
        $this->joomla->getApplication();
        $renderer = $this->joomla->getDocument()->loadRenderer('module');
        $modules = \JModuleHelper::getModules($zone);
        $modulesClones = [];
        foreach ($modules as $module) {
            $params = $module->params;
            $this->overrideParams($module, $override);
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
            $result = $twig->render($parameters['decorator'], [
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
     * @param string $string
     * @param array  $replace
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function trans($string, $replace = [])
    {
        $text = \JText::_($string);

        foreach ($replace as $key => $value) {
            $text = str_replace($key, $value, $text);
        }

        return $text;
    }

    public function getName()
    {
        return 'joomla_module';
    }
}
