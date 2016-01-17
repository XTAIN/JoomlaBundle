<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Component\Symfony\Field;

use XTAIN\Bundle\JoomlaBundle\Component\Field\SymfonyModules as SymfonyModulesField;
use XTAIN\Bundle\JoomlaBundle\Component\Module\ModuleManager;
use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;

class SymfonyModules implements DependencyFactoryInterface
{
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
    public function setModuleManager(ModuleManager $manager)
    {
        self::$manager = $manager;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        SymfonyModulesField::setModuleManager(self::$manager);
    }
}