<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Component\Module;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use XTAIN\Bundle\JoomlaBundle\Entity\Module;

/**
 * Class ModuleManager
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Component\Module
 */
class ModuleManager implements ContainerAwareInterface
{
    /**
     * @var array
     */
    protected $moduleServices = [];

    /**
     * @var array
     */
    protected $modules = [];

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function __construct()
    {
        $this->moduleServices = [];
    }

    public function addModuleService($id)
    {
        $this->moduleServices[] = $id;
    }

    /**
     * @return array
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getModuleServices()
    {
        return $this->moduleServices;
    }

    /**
     * @param Module $module
     *
     * @return ModuleRendererInterface
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getModuleRenderer(Module $module)
    {
        $params = $module->getParams();

        if (!isset($params['service'])) {
            throw new \InvalidArgumentException(sprintf(
                'Module %s has no service parameter',
                $module->getId()
            ));
        }

        $id = $params['service'];

        if (!in_array($id, $this->moduleServices)) {
            throw new \InvalidArgumentException(sprintf(
                'Service %s is not a model service',
                $id
            ));
        }

        if (isset($this->modules[$module->getId()])) {
            return $this->modules[$module->getId()];
        }

        /** @var $service ModuleRendererInterface */
        $service = $this->modules[$module->getId()] = $this->container->get($id);
        $service->setModule($module);

        return $service;
    }
}
