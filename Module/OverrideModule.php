<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Module;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
        $languge->register('join', function($context, $data, $glue) {
            return sprintf('implode(%1$s, %2$s)', var_export($data, true), $glue);
        }, function($context, $data, $glue) {
            return implode($glue, $data);
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

    /**
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function render()
    {
        $params = $this->module->getParams();

        $module = null;
        if (isset($params['settings']) && isset($params['settings']['module'])) {
            $module = $params['settings']['module'];
        }

        $overrideModule = $this->helper->getModuleById($this->module->getId());
        $module = $this->helper->getModuleById($module);

        if ($module === null) {
            return '';
        }

        $override = $this->findOverride();

        $overrideParams = $this->computeOverrideParmas($override);

        $module = clone $module;

        if (isset($overrideParams['title'])) {
            $overrideModule->title = $overrideParams['title'];
        }

        $this->helper->renderModuleObject($module, [], $overrideParams['params']);

        return $module->content;
    }
}