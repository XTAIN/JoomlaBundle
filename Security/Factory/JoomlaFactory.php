<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class JoomlaFactory
 *
 * @author Maximilian Ruta <mr@xtain.net>
 */
class JoomlaFactory implements SecurityFactoryInterface
{
    /**
     * @param ContainerBuilder $container
     * @param int              $id
     * @param array            $config
     * @param string           $userProvider
     * @param string           $defaultEntryPoint
     *
     * @return array
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.' . $id;
        $container
            ->setDefinition(
                $providerId,
                new DefinitionDecorator('joomla.security.authentication.provider')
            )
            ->addMethodCall('setUserProvider', [new Reference($userProvider)]);

        $listenerId = 'security.authentication.listener.' . $id;
        $container->setDefinition(
            $listenerId,
            new DefinitionDecorator('joomla.security.authentication.listener')
        );

        return [$providerId, $listenerId, $defaultEntryPoint];
    }

    /**
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getPosition()
    {
        return 'remember_me';
    }

    /**
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getKey()
    {
        return 'joomla';
    }

    /**
     * @param NodeDefinition $builder
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function addConfiguration(NodeDefinition $builder)
    {
    }
}
