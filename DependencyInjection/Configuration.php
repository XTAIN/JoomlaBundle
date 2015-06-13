<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    protected $alias;

    /**
     * @param string $alias
     */
    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->alias);

        $rootNode
            ->children()
                ->arrayNode('install')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('resource')->end()
                                ->scalarNode('target')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('override')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')
                                ->isRequired()
                            ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('override')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('file')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->enumNode('static')
                                ->defaultValue('self')
                                ->values(['self', 'static'])
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->variableNode('config')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
