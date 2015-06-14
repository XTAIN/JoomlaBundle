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
use Symfony\Component\HttpKernel\KernelInterface;
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
     * @param ContainerBuilder $container
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function loadJoomlaConfig(ContainerBuilder $container)
    {
        if (!defined('JPATH_CONFIGURATION')) {
            define('JPATH_CONFIGURATION', $container->getParameter('joomla.config_dir'));
        }

        // Pre-Load configuration. Don't remove the Output Buffering due to BOM issues, see JCode 26026
        ob_start();
        if (file_exists(JPATH_CONFIGURATION . '/configuration.php')) {
            $config = OverrideUtils::classReplace(JPATH_CONFIGURATION . '/configuration.php', 'JConfig', 'JProxy_Config');
        } else {
            $config = 'class JProxy_Config {}';
        }
        eval($config);
        ob_end_clean();
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return array
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getJoomlaConfig(ContainerBuilder $container)
    {
        $this->loadJoomlaConfig($container);

        return get_class_vars('JProxy_Config');
    }

    /**
     * @return array
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getDefaultConfig()
    {
        $config = Yaml::parse(__DIR__.'/../Resources/config/default_joomla_config.yml');

        if (!is_array($config)) {
            return [];
        }

        return $config;
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     *
     * @return array
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function prepareConfig(array $config, ContainerBuilder $container)
    {
        $default = $this->getJoomlaConfig($container);

        $reset = [
            'debug',
            'log_path',
            'tmp_path',
            'secret'
        ];

        foreach ($default as $key => $value) {
            if (in_array($key, $reset)) {
                unset($default[$key]);
            }
        }

        $config = array_merge(
            $default,
            $this->getDefaultConfig(),
            $config
        );

        return $config;
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configs[] = Yaml::parse(__DIR__.'/../Resources/config/override.yml');

        $configuration = new Configuration($this->getAlias());
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

        if (!isset($config['config'])) {
            $config['config'] = [];
        }

        $config['config'] = $this->prepareConfig($config['config'], $container);

        $configFactoryDef = $container->getDefinition('joomla.factory.config');
        $configFactoryDef->addMethodCall('setConfiguration', [ $config['config'] ]);

        if ($container->getParameter('joomla.root_dir') === null) {
            $reflector = new \ReflectionClass('Composer\Autoload\ClassLoader');
            $vendorDir = dirname(dirname($reflector->getFileName()));
            $container->setParameter(
                'joomla.root_dir',
                $vendorDir . DIRECTORY_SEPARATOR . 'joomla' . DIRECTORY_SEPARATOR . 'joomla-cms'
            );
        }

        $container->setParameter('joomla.installations', $this->installations);

        if (isset($config['orm']['entity_manager'])) {
            $container->setAlias('joomla.orm.entity_manager', $config['orm']['entity_manager']);
        }
    }
}
