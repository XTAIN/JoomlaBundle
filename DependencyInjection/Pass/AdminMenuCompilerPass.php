<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\DependencyInjection\Pass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AdminMenuCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $menuManagerDefinition = $container->getDefinition('joomla.admin.menu_manager');

        $adminMenus = $container->findTaggedServiceIds('joomla.admin_menu');
        $references = array();

        foreach ($adminMenus as $id => $dummy) {
            $references[] = new Reference($id);
        }

        $menuManagerDefinition->addArgument($references);

    }
}