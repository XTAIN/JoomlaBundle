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
use Symfony\Component\Process\Process;
use XTAIN\Bundle\JoomlaBundle\Installation\Configuration;
use XTAIN\Bundle\JoomlaBundle\Installation\InstallerInterface;

/**
 * Class InstallationCommand
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Command
 */
class InstallationCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('xtain:joomla:install')
            ->setDefinition(
                [
                    new InputOption('username', null, InputOption::VALUE_REQUIRED, 'The username for the admin'),
                    new InputOption('email', null, InputOption::VALUE_REQUIRED, 'The E-Mail for admin'),
                    new InputOption('password', null, InputOption::VALUE_REQUIRED, 'The password for admin'),
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
        $configuration = new Configuration();

        $configuration->setUsername($input->getOption('username'));
        $configuration->setEmail($input->getOption('email'));
        $configuration->setPassword($input->getOption('password'));

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        /** @var InstallerInterface $installer */
        $installer = $this->getContainer()->get('joomla.installation.installer');

        if ($configuration->getUsername() === null) {
            $default = 'admin';
            $question = new Question(
                sprintf('Please enter the name of the Joomla admin user (%s): ', $default),
                $default
            );
            $configuration->setUsername($helper->ask($input, $output, $question));
        }

        if ($configuration->getEmail() === null) {
            $localdomain = gethostname();

            // there is no native way to do this in php
            $process = new Process('/bin/hostname -d');
            $process->run();

            if ($process->isSuccessful()) {
                $hostname = trim($process->getOutput());
                if (!empty($hostname)) {
                    $localdomain = $hostname;
                }
            }

            $default = $configuration->getUsername() . '@' . $localdomain;
            $question = new Question(
                sprintf('Please enter the mail address of the Joomla admin user (%s): ', $default),
                $default
            );

            $configuration->setEmail($helper->ask($input, $output, $question));
        }

        if ($configuration->getPassword() === null) {
            $question = new Question(
                'Please enter a password for the Joomla admin user: '
            );
            $question->setHidden(true);

            $configuration->setPassword($helper->ask($input, $output, $question));
        }

        $installer->install($configuration);
    }
}