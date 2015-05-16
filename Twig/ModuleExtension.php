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
            new \Twig_SimpleFunction('joomla_module_position', [$this, 'renderModulePosition'], ['is_safe' => ['html']])
        ];
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

        if ($params !== false) {
            $params = array_merge($params, $override);
            $module->params = json_encode($params);
        }
    }

    public function renderModulePosition($zone, array $parameters = [], array $override = [])
    {
        $this->joomla->getApplication();
        $renderer = $this->joomla->getDocument()->loadRenderer('module');
        $modules = \JModuleHelper::getModules($zone);
        $html = '';
        foreach ($modules as $module) {
            $params = $module->params;
            $this->overrideParams($module, $override);
            $html .= $renderer->render($module, $parameters);
            $module->params = $params;
        }

        return $html;
    }

    public function getName()
    {
        return 'joomla_module';
    }
}
