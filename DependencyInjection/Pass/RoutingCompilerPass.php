<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\DependencyInjection\Pass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Routing\RouterInterface;
use XTAIN\Bundle\JoomlaBundle\Routing\UrlGenerator;

/**
 * Class RoutingCompilerPass
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\DependencyInjection\Pass
 */
class RoutingCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function process(ContainerBuilder $container)
    {
        $container->setAlias('router', 'joomla.router');

        $definition = $container->getDefinition('joomla.router');

        $taggedServices = $container->findTaggedServiceIds(
            'joomla.router'
        );

        $interface = RouterInterface::class;

        $sortedRouters = [];

        foreach ($taggedServices as $id => $tagAttributes) {
            $tagDefinition = $container->getDefinition($id);
            $tagClass = $container->getParameterBag()->resolveValue($tagDefinition->getClass());
            $tagClass = new \ReflectionClass($tagClass);

            if (!$tagClass->implementsInterface($interface)) {
                throw new \LogicException(
                    sprintf("Class of service %s does not implement interface %s", $id, $interface)
                );
            }

            $tagAttributes = current($tagAttributes);

            $priority = 0;
            if (isset($tagAttributes['priority'])) {
                $priority = $tagAttributes['priority'];
            }

            $sortedRouters[] = [$priority, new Reference($id)];
        }

        usort(
            $sortedRouters,
            function ($serviceA, $serviceB) {
                if ($serviceA[0] == $serviceB[0]) {
                    return 0;
                }

                return ($serviceA[0] < $serviceB[0]) ? 1 : -1;
            }
        );

        $sortedRouters = array_reverse($sortedRouters);

        foreach ($sortedRouters as $service) {
            $definition->addMethodCall(
                'addRouter',
                [$service[1]]
            );
        }

        $container->setParameter('router.options.generator_class', UrlGenerator::class);
        $container->setParameter('router.options.generator_base_class', UrlGenerator::class);
    }
}
