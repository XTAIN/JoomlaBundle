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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExtensionInstallationCommand
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Command
 */
class ExtensionInstallationCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('xtain:joomla:install:extension')
            ->setDefinition(
                [
                    new InputArgument('type', InputArgument::REQUIRED, 'The type of the extension'),
                    new InputArgument('name', InputArgument::REQUIRED, 'The name of the extension'),
                    new InputOption(
                        'register-only',
                        null,
                        InputOption::VALUE_NONE,
                        'Only register extension without running installation scripts'
                    )
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
        $type = $input->getArgument('type');
        $name = $input->getArgument('name');
        $registerOnly = $input->getOption('register-only');

        if (!$registerOnly) {
            throw new \InvalidArgumentException('Currently only calls with --register-only option are implemented.');
        }

        /** @var \XTAIN\Bundle\JoomlaBundle\Installation\ExtensionInstaller $extensionInstaller */
        $extensionInstaller = $this->getContainer()->get('joomla.installation.extension_installer');

        if ($extensionInstaller->isExtensionInstalled($name)) {
            $output->writeln(sprintf(
                'Extension %s is installed, activate it.',
                $name
            ));
        } else {
            $output->writeln(sprintf(
                'Extension %s is not installed, install it.',
                $name
            ));
        }

        $extensionInstaller->registerExtension($name, $type);
    }
}