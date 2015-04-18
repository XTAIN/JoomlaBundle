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
use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;

/**
 * Class OverrideCompilerPass
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\DependencyInjection\Pass
 */
class OverrideCompilerPass implements CompilerPassInterface
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
        if (!$container->hasDefinition('joomla.warmer.patcher')) {
            return;
        }

        $loaderFactoryDefinition = $container->getDefinition('joomla.factory.loader');
        $warmerDefinition = $container->getDefinition('joomla.warmer.patcher');
        $taggedServices = $container->findTaggedServiceIds('joomla.service');

        foreach ($taggedServices as $id => $attributesBag) {
            $taggedServiceDefinition = $container->getDefinition($id);
            foreach ($attributesBag as $attributes) {
                $taggedServiceClass = $container->getParameterBag()->resolveValue(
                    $taggedServiceDefinition->getClass()
                );
                $joomlaClass = $taggedServiceClass;
                if (isset($attributes['class'])) {
                    $joomlaClass = $container->getParameterBag()->resolveValue($attributes['class']);
                }

                if ($joomlaClass != $taggedServiceClass) {
                    $file = false;
                    if (!empty($attributes['file'])) {
                        $file = $container->getParameterBag()->resolveValue($attributes['file']);
                    }
                    $static = false;
                    if (!empty($attributes['static'])) {
                        $static = $container->getParameterBag()->resolveValue($attributes['static']);
                    }
                    $warmerDefinition->addMethodCall(
                        'addOverride',
                        [
                            [
                                'class'    => $joomlaClass,
                                'file'     => $file,
                                'static'   => $static,
                                'override' => $taggedServiceClass
                            ]
                        ]
                    );
                }

                if (isset($attributes['factory'])) {
                    $factoryService = $container->getParameterBag()->resolveValue($attributes['factory']);
                } else {
                    $factoryService = $taggedServiceDefinition->getFactoryService();
                }

                if ($factoryService != null) {
                    $factoryServiceDefinition = $container->getDefinition($factoryService);
                    $factoryServiceClass = $container->getParameterBag()->resolveValue(
                        $factoryServiceDefinition->getClass()
                    );

                    if (is_a($factoryServiceClass, DependencyFactoryInterface::CLASS, true)) {
                        $loaderFactoryDefinition->addMethodCall(
                            'addDependencyFactory',
                            [
                                $joomlaClass,
                                $factoryService
                            ]
                        );
                    }
                }
            }
        }
    }
}
