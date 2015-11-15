<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\DependencyInjection\Pass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use XTAIN\Bundle\JoomlaBundle\Component\Module\ModuleRendererInterface;

/**
 * Class ModuleCompilerPass
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\DependencyInjection\Pass
 */
class ModuleCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('joomla.component.module.module_manager')) {
            return;
        }

        $moduleManagerDefinition = $container->getDefinition('joomla.component.module.module_manager');
        $taggedServices = $container->findTaggedServiceIds('joomla.module');

        foreach (array_keys($taggedServices) as $id) {
            $moduleDefinition = $container->getDefinition($id);
            $reflection = new \ReflectionClass($moduleDefinition->getClass());

            if (!$reflection->implementsInterface(ModuleRendererInterface::class)) {
                throw new \InvalidArgumentException(sprintf(
                    'Module service class %s does not implement %s',
                    $id,
                    ModuleRendererInterface::class
                ));
            }

            $moduleDefinition->setScope(ContainerInterface::SCOPE_PROTOTYPE);
            $moduleManagerDefinition->addMethodCall('addModuleService', [ $id ]);
        }
    }
}