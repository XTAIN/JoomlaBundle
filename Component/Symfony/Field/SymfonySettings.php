<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Component\Symfony\Field;

use XTAIN\Bundle\JoomlaBundle\Component\Field\SymfonySettings as SymfonySettingsField;
use XTAIN\Bundle\JoomlaBundle\Component\Module\ModuleManager;
use XTAIN\Bundle\JoomlaBundle\Entity\ModuleRepositoryInterface;
use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;

class SymfonySettings implements DependencyFactoryInterface
{
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
    public function setModuleManager(ModuleManager $manager)
    {
        self::$manager = $manager;
    }

    /**
     * @param ModuleRepositoryInterface $repository
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setModuleRepository(ModuleRepositoryInterface $repository)
    {
        self::$repository = $repository;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        SymfonySettingsField::setModuleManager(self::$manager);
        SymfonySettingsField::setModuleRepository(self::$repository);
    }
}