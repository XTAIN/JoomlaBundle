<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Component\Field;

use XTAIN\Bundle\JoomlaBundle\Component\Module\ModuleManager;

\JFormHelper::loadFieldClass('list');

class SymfonyModules extends \JFormFieldList
{

    protected $type = 'SymfonyModules';

    /**
     * @var ModuleManager
     */
    protected static $manager;

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
     * @return array
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function getModuleServices()
    {
        return self::$manager->getModuleServices();
    }

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     *
     * @since   11.1
     */
    protected function getOptions()
    {
        $options = array();

        foreach ($this->getModuleServices() as $module)
        {
            // Create a new option object based on the <option /> element.
            $tmp = \JHtml::_(
                'select.option', $module,
                \JText::_(trim((string) $module)), 'value', 'text'
            );

            // Add the option object to the result set.
            $options[] = $tmp;
        }

        reset($options);

        return $options;
    }
}

\XTAIN\Bundle\JoomlaBundle\Library\Loader::injectStaticDependencies(SymfonyModules::class);
