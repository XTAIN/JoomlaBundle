<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Module;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use XTAIN\Bundle\JoomlaBundle\Component\Module\AbstractModule;
use XTAIN\Bundle\JoomlaBundle\Entity\Module;
use XTAIN\Bundle\JoomlaBundle\Entity\ModuleRepositoryInterface;
use XTAIN\Bundle\JoomlaBundle\Joomla\JoomlaHelper;

class OverrideModule extends AbstractModule
{
    /**
     * @var JoomlaHelper
     */
    protected $helper;

    /**
     * @var ModuleRepositoryInterface
     */
    protected $moduleRepository;

    /**
     * OverrideModule constructor.
     *
     * @param JoomlaHelper $helper
     */
    public function __construct(
        JoomlaHelper $helper,
        ModuleRepositoryInterface $moduleRepository
    ) {
        $this->helper = $helper;
        $this->moduleRepository = $moduleRepository;
    }

    public function getFormTemplate()
    {
        return 'XTAINJoomlaBundle::Joomla/Module/Override/settings.html.twig';
    }

    /**
     * @return array
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function getMenuTree()
    {
        require_once realpath(JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php');

        $items = array();

        foreach (\MenusHelper::getMenuLinks() as $menu) {
            foreach ($menu->links as $item) {
                $spacer = str_repeat('- ', $item->level);
                $items[$menu->menutype . '-' . $item->value] = $spacer . $item->text;
            }
        }

        return $items;

    }

    /**
     * @return array
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function getModules()
    {
        $items = array();

        /** @var Module $module */
        foreach ($this->moduleRepository->findBy(array(
                                                     'client' => 0
                                                 )) as $module) {
            $items[$module->getId()] = $module->getTitle() . ' (ID: ' . $module->getId() . ')';
        }

        return $items;

    }

    /**
     * @return \Symfony\Component\Form\FormView
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getSettingsFormView()
    {
        $builder = $this->createFormBuilder();

        $modules = $this->getModules();
        $menuTree = $this->getMenuTree();

        $settings = $builder->get('params')->get('settings');
        $settings->add('module', ChoiceType::class, array(
            'choices' => $modules
        ));

        $settings->add('showExpr', TextType::class, array(
            'required' => false
        ));

        $settings->add('showPhp', TextareaType::class, array(
            'required' => false
        ));

        $paramsBuilder = $builder->create('params', 'form');
        $itemBuilder = $builder->create('item', 'form', [
            'label' => false
        ]);
        $itemBuilder->add($paramsBuilder);
        $itemBuilder->add('menu', ChoiceType::class, array(
            'choices' => array_merge(
                array('all' => 'All'),
                $menuTree
            ),
            'group_by' => function($val) {
                if ($val == '') {
                    return null;
                }

                return substr($val, 0, strrpos($val, '-'));
            }
        ));
        $itemBuilder->add('title', TextType::class, array(
            'required' => false
        ));
        $itemBuilder->add('title_type', ChoiceType::class, array(
            'choices' => array(
                'value' => 'Value',
                'menu_expr' => 'Menu Expression'
            )
        ));

        $builder->add($itemBuilder);

        return $builder->getForm()->createView();
    }


    /**
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function renderSettings()
    {
        $params = $this->module->getParams();

        $overrideModule = null;
        if (isset($params['settings']) && isset($params['settings']['module'])) {
            $moduleId = $params['settings']['module'];
            $overrideModule = $this->moduleRepository->find($moduleId);
        }

        $overrides = array();
        if (isset($params['settings']) && isset($params['settings']['overrides'])) {
            $overrides = $params['settings']['overrides'];
        }

        return $this->twig->render(
            $this->getFormTemplate(),
            [
                'overrides' => $overrides,
                'override_module' => $overrideModule,
                'form_view' => $this->getSettingsFormView()
            ]
        );
    }

    protected function findParents()
    {
        $parents = array();

        $app = \JFactory::getApplication();
        $menu = $app->getMenu();

        if ($menu) {
            $active = $menu->getActive();
            if ($active) {
                $tree = array_reverse($active->tree);

                foreach ($tree as $id) {
                    $parents[] = $menu->getItem($id);
                }
            }
        }

        return $parents;
    }

    protected function findOverride()
    {
        $params = $this->module->getParams();

        $overrides = array();
        if (isset($params['settings']) && isset($params['settings']['overrides'])) {
            $overrides = $params['settings']['overrides'];
        }

        $parents = $this->findParents();

        foreach ($parents as $parent) {
            $key = $parent->menutype . '-' . $parent->id;

            if (isset($overrides[$key])) {
                return $overrides[$key];
            }
        }

        if (isset($overrides['all'])) {
            return $overrides['all'];
        }

        return null;
    }

    protected function computeMenuExpression($override, $expression)
    {
        $languge = new ExpressionLanguage();
        $languge->register('join', function($data, $glue) {
            return sprintf('implode(%1$s, %2$s)', var_export($data, true), $glue);
        }, function($context, $data, $glue) {
            return implode($glue, $data);
        });
        $languge->register('length', function($data) {
            return sprintf('is_array(%1$s) ? count(%2$s) : strlen(%3$s)', var_export($data, true), var_export($data, true), var_export($data, true));
        }, function($context, $data) {
            return is_array($data) ? count($data) : strlen($data);
        });

        $parents = $this->findParents();

        foreach ($parents as $menu) {
            $result = $languge->evaluate($expression, array(
                'override' => $override,
                'menu' => $menu
            ));

            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    protected function computeOverrideParmas($override)
    {
        $overrideParams = array(
            'params' => array()
        );
        $params = array();

        if (isset($override['params'])) {
            $params = $override['params'];
        }

        foreach ($params as $name => $param) {
            switch ($param['type']) {
                case 'value':
                    $overrideParams['params'][$name] = $param['value'];
                    break;
                case 'menu_expr':
                    $value = $this->computeMenuExpression($override, $param['value']);
                    if ($value !== null) {
                        $overrideParams['params'][$name] = $value;
                    }
                    break;
            }
        }

        if (isset($override['title']) && !empty($override['title'])) {
            switch ($override['titleType']) {
                case 'value':
                    $overrideParams['title'] = $override['title'];
                    break;
                case 'menu_expr':
                    $value = $this->computeMenuExpression($override, $override['title']);
                    if ($value !== null) {
                        $overrideParams['title'] = $value;
                    }
                    break;
            }
        }

        return $overrideParams;
    }

    protected function evCode($code) {
        return eval($code);
    }

    /**
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function render(&$paramsRef)
    {
        $params = $this->module->getParams();

        $module = null;
        if (isset($params['settings']) && isset($params['settings']['module'])) {
            $module = $params['settings']['module'];
        }

        $showExpr = null;
        if (isset($params['settings']) && isset($params['settings']['showExpr'])) {
            $showExpr = $params['settings']['showExpr'];
        }

        $showPhp = null;
        if (isset($params['settings']) && isset($params['settings']['showPhp'])) {
            $showPhp = $params['settings']['showPhp'];
        }

        $overrideModule = $this->helper->getModuleById($this->module->getId());
        $module = $this->helper->getModuleById($module);

        if ($module === null) {
            return '';
        }

        $override = $this->findOverride();

        if (!empty($showExpr)) {
            $value = $this->computeMenuExpression($override, $showExpr);
            if ($value === null) {
                return '';
            }
            if (!$value) {
                return '';
            }
        }

        if (!empty($showPhp)) {
            $show = $this->evCode($showPhp);
            if ($show !== null) {
                if (!$show) {
                    return '';
                }
            }
        }

        $parentParams = json_decode($module->params);

        $inheritParams = array(
            'moduleclass_sfx',
            'header_tag',
            'bootstrap_size',
            'module_tag',
            'header_class'
        );

        foreach ($inheritParams as $inheritParam) {
            $paramsRef->set($inheritParam, $parentParams->{$inheritParam});
        }

        $overrideParams = $this->computeOverrideParmas($override);

        foreach ($inheritParams as $inheritParam) {
            if (isset($overrideParams['params'][$inheritParam])) {
                $paramsRef->set($inheritParam, $overrideParams['params'][$inheritParam]);
            }
        }

        $module = clone $module;

        if (isset($overrideParams['title'])) {
            $overrideModule->title = $overrideParams['title'];
            if (strlen($overrideModule->title) <= 0) {
                $overrideModule->showtitle = 0;
            }
        } else {
            $overrideModule->title = $module->title;
        }

        $this->helper->renderModuleObject($module, [], $overrideParams['params']);

        return $module->content;
    }
}
