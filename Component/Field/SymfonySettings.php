<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Component\Field;

use XTAIN\Bundle\JoomlaBundle\Component\Module\ModuleManager;
use XTAIN\Bundle\JoomlaBundle\Entity\ModuleRepositoryInterface;

class SymfonySettings extends \JFormField
{
    protected $type = 'symfonysettings';

    /**
     * @var ModuleManager
     */
    protected static $manager;

    /**
     * @var ModuleRepositoryInterface
     */
    protected static $repository;

    /**
     * @param ModuleManager $manager
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setModuleManager(ModuleManager $manager)
    {
        self::$manager = $manager;
    }

    /**
     * @param ModuleRepositoryInterface $repository
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setModuleRepository(ModuleRepositoryInterface $repository)
    {
        self::$repository = $repository;
    }

    /**
     * Method to get a control group with label and input.
     *
     * @param   array  $options  Options to be passed into the rendering of the field
     *
     * @return  string  A string containing the html for the control group
     *
     * @since   3.2
     */
    public function renderField($options = array())
    {
        return $this->getInput();
    }

    public function getInput()
    {
        $service = $this->form->getData()->get('params.service');
        $moduleId = $this->form->getData()->get('id');

        if (empty($service) || empty($moduleId)) {
            return \JText::_('MOD_SYMFONY_FIELD_SETTINGS_NOSETTINGS');
        }

        $module = self::$repository->find($moduleId);
        $moduleRenderer = self::$manager->getModuleRenderer($module);

        return $moduleRenderer->renderSettings();
    }
}

\XTAIN\Bundle\JoomlaBundle\Library\Loader::injectStaticDependencies(SymfonySettings::class);
