<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\DependencyInjection\Pass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use XTAIN\Bundle\JoomlaBundle\Library\Joomla\Database\Driver\AbstractDoctrineDriver;

class DoctrineCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('joomla.factory.database.driver.doctrine')) {
            return;
        }

        $doctrineFactory = $container->getDefinition('joomla.factory.database.driver.doctrine');
        $taggedServices = $container->findTaggedServiceIds('joomla.database');

        foreach (array_keys($taggedServices) as $id) {
            $platformDefinition = $container->getDefinition($id);
            $doctrineFactory->addMethodCall('addDatabasePlatform', [ $platformDefinition->getClass() ]);
        }

    }
}