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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @author Maximilian Ruta <mr@xtain.net>
 */
class XTAINJoomlaExtension extends Extension
{
    /**
     * @var array
     */
    protected $installations = [];

    /**
     * @param string $name
     * @param string $resource
     * @param string $target
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function pathInstall($name, $resource, $target)
    {
        $this->installations[$name][] = [
            'resource' => $resource,
            'target'   => $target
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configs[] = Yaml::parse(__DIR__.'/../Resources/config/override.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (isset($config['install'])) {
            foreach ($config['install'] as $name => $installables) {
                foreach ($installables as $installable) {
                    $this->pathInstall($name, $installable['resource'], $installable['target']);
                }
            }
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $warmerPatcherDef = $container->getDefinition('joomla.warmer.patcher');
        if (isset($config['override'])) {
            foreach ($config['override'] as $override) {
                $warmerPatcherDef->addMethodCall('addOverride', [ $override ]);
            }
        }

        if ($container->getParameter('joomla.root_dir') === null) {
            $reflector = new \ReflectionClass('Composer\Autoload\ClassLoader');
            $vendorDir = dirname(dirname($reflector->getFileName()));
            $container->setParameter(
                'joomla.root_dir',
                $vendorDir . DIRECTORY_SEPARATOR . 'joomla' . DIRECTORY_SEPARATOR . 'joomla-cms'
            );
        }

        $container->setParameter('joomla.installations', $this->installations);
    }
}
