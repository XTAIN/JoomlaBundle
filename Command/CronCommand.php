<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use XTAIN\Bundle\JoomlaBundle\Joomla\ApplicationClosedException;

/**
 * Class CronCommand
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Command
 */
class CronCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('xtain:joomla:cli')
            ->setDefinition(
                [
                    new InputArgument('task', InputArgument::REQUIRED)
                ]
            );
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return void
     * @throws \InvalidArgumentException When the target directory does not exist
     * @throws \InvalidArgumentException When symlink cannot be used
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rootDir = $this->getContainer()->getParameter('joomla.root_dir') . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR;
        $script = $input->getArgument('task');

        if (!file_exists($rootDir . $script . '.php')) {
            throw new \InvalidArgumentException('script of type ' . $script . ' not found');
        }

        define('_JDEFINES', 1);
        define('JAPPLICATION_TYPE', 'cli');
        define('JPATH_BASE', $rootDir);

        $application = JAPPLICATION_TYPE;
        $cacheDir =
            rtrim(
                $this->getContainer()->getParameter('joomla.cache_dir') . DIRECTORY_SEPARATOR . strtolower($application),
                DIRECTORY_SEPARATOR
            );

        if (!defined('JPATH_CACHE')) {
            define('JPATH_CACHE', $cacheDir);
        }

        $filesystem = new Filesystem();
        if (!$filesystem->exists(JPATH_CACHE)) {
            $filesystem->mkdir(JPATH_CACHE);
        }

        $this->getContainer()->enterScope('request');

        try {
            require_once $rootDir . $script . '.php';
        } catch (ApplicationClosedException $e) {
            // nothing
        }
    }
}