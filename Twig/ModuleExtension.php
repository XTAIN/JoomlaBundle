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
use XTAIN\Bundle\JoomlaBundle\Joomla\JoomlaHelper;

/**
 * Class ModuleExtension
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Twig
 */
class ModuleExtension extends \Twig_Extension implements JoomlaAwareInterface
{
    /**
     * @var string
     */
    const TITLE_SEPARATOR = ' - ';

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
            new \Twig_SimpleFunction('joomla_trans', [$this, 'trans']),
            new \Twig_SimpleFunction('joomla_pagetitle', [$this, 'pagetitle']),
            new \Twig_SimpleFunction('joomla_pageclass', [$this, 'pageclass'])
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

    public function component($directRender = false)
    {
        $document = \JFactory::getDocument();

        if ($directRender) {
            return $document->getBuffer('component');
        } else {
            return '<jdoc:include type="component" />';
        }
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
        return JoomlaHelper::trans($string, $replace);
    }

    /**
     * @return array
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function pagetitle()
    {
        $app = \JFactory::getApplication();
        $document = \JFactory::getDocument();
        $documentTitle = $document->getTitle();

        if ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $pagetTitleWithoutSitename = preg_quote(\JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), ''), '/');

            if (preg_match('/^' . $pagetTitleWithoutSitename, $documentTitle)) {
                $documentTitle = preg_replace('/^' . $pagetTitleWithoutSitename, '', $documentTitle);
            }
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $pagetTitleWithoutSitename = preg_quote(\JText::sprintf('JPAGETITLE', '', $app->getCfg('sitename')), '/');

            if (preg_match('/' . $pagetTitleWithoutSitename . '$/', $documentTitle)) {
                $documentTitle = preg_replace('/' . $pagetTitleWithoutSitename . '$/', '', $documentTitle);
            }
        }

        $subtitle = '';

        $itemid = \JRequest::getVar('Itemid');
        $menu = $app->getMenu();
        $active = $menu->getItem($itemid);
        $completeTitle = null;
        if (is_object($active)) {
            $completeTitle = html_entity_decode($active->title);
            $subtitle = $active->params->get('menu-anchor_title');
        }

        if (empty($completeTitle)) {
            $completeTitle = $documentTitle;
        }

        $title = $completeTitle;

        if (empty($subtitle)) {
            $pos = strpos($completeTitle, self::TITLE_SEPARATOR);
            if ($pos !== false) {
                $title = substr($completeTitle, 0, $pos);
                $subtitle = substr($completeTitle, 0, $pos + strlen(self::TITLE_SEPARATOR));
            }
        }

        return [
            'title'    => $title,
            'subtitle' => $subtitle
        ];
    }

    /**
     * @return array
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function pageclass()
    {
        $itemid = \JRequest::getVar('Itemid');
        $menu = \JFactory::getApplication()->getMenu();
        $active = $menu->getItem($itemid);

        $pageclass = '';
        if (is_object($active)) {
            $params = $menu->getParams($active->id);
            $pageclass = $params->get('pageclass_sfx');
        }

        return $pageclass;
    }

    public function getName()
    {
        return 'joomla_module';
    }
}
