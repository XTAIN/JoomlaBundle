<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Installation;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Asset
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Installation
 */
class Asset
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * AssetHandler constructor.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function addToConfig($items)
    {
        $itemKeys = array_keys($items);

        $root = 'app';
        $path = $this->kernel->getRootDir() . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'override.yml';

        $data = Yaml::parse(file_get_contents($path));

        if (!isset($data['xtain_joomla'])) {
            $data['xtain_joomla'] = array();
        }

        if (!isset($data['xtain_joomla']['install'])) {
            $data['xtain_joomla']['install'] = array();
        }

        if (!isset($data['xtain_joomla']['install'])) {
            $data['xtain_joomla']['install'] = array();
        }

        if (!isset($data['xtain_joomla']['install'][$root])) {
            $data['xtain_joomla']['install'][$root] = array();
        }

        foreach ($data['xtain_joomla']['install'] as $group => $groupItems) {
            foreach ($groupItems as $item) {
                if (in_array($item['resource'], $itemKeys)) {
                    unset($items[$item['resource']]);
                }
            }
        }

        foreach ($items as $resource => $target) {
            $data['xtain_joomla']['install'][$root][] = array(
                'resource' => $resource,
                'target' => $target
            );
        }

        file_put_contents($path, Yaml::dump($data, 4));
    }

    protected static function getPhpArguments()
    {
        $arguments = array();

        $phpFinder = new PhpExecutableFinder();
        if (method_exists($phpFinder, 'findArguments')) {
            $arguments = $phpFinder->findArguments();
        }

        if (false !== $ini = php_ini_loaded_file()) {
            $arguments[] = '--php-ini='.$ini;
        }

        return $arguments;
    }

    protected static function getPhp($includeArgs = true)
    {
        $phpFinder = new PhpExecutableFinder();
        if (!$phpPath = $phpFinder->find($includeArgs)) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }

        return $phpPath;
    }

    /**
     * @return void
     * @throws \Exception
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function install()
    {
        $root = $this->kernel->getRootDir() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
        $php = static::getPhp(false);
        $builder = new ProcessBuilder();

        $builder->setPrefix($php);

        $process = $builder
            ->setArguments(array_merge(
                self::getPhpArguments(),
                array(
                    'bin/console',
                    'xtain:joomla:assets:install',
                    '--mode=relative',
                    $root . 'web'
                )
            ))
            ->setWorkingDirectory($root)
            ->getProcess();

        $process->run();
        $process->wait();
    }
}