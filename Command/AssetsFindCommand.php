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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

/**
 * Command that places bundle web assets into a given directory.
 *
 * @author Maximilian Ruta <mr@xtain.net>
 */
class AssetsFindCommand extends ContainerAwareCommand
{
    const STATUS_OTHER = '#';

    const STATUS_UNKNOWN = '??';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('xtain:joomla:assets:find')
            ->setDefinition(
                [
                    new InputOption('force', null, InputOption::VALUE_NONE),
                    new InputArgument('target', InputArgument::OPTIONAL, 'The target directory', 'web'),
                ]
            );
    }

    public function parseStatusOutput($output, $allowedStatus = [self::STATUS_UNKNOWN])
    {
        $files = [];

        $parts = explode("\0", $output);

        foreach ($parts as $line) {
            if (empty($line)) {
                continue;
            }

            list($status, $file) = explode(" ", $line);
            $status = trim($status);
            $file = trim($file);

            $file = rtrim($file, '/\\');

            switch ($status) {
                case self::STATUS_OTHER:
                    if (in_array(self::STATUS_OTHER, $allowedStatus)) {
                        $files[$file] = self::STATUS_OTHER;
                    }
                    break;
                default:
                    if (in_array(self::STATUS_UNKNOWN, $allowedStatus)) {
                        $files[$file] = self::STATUS_UNKNOWN;
                    }
                    break;
            }
        }

        return $files;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException When the target directory does not exist or symlink cannot be used
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $force = false;
        if ($input->getOption('force')) {
            $force = true;
        }

        if ($force) {
            $output->writeln('Run in <comment>force</comment> mode');
        } else {
            $output->writeln('Run in <comment>dry run</comment> mode');
        }

        $targetArg = rtrim($input->getArgument('target'), '/\\');

        if (!is_dir($targetArg)) {
            throw new \InvalidArgumentException(
                sprintf('The target directory "%s" does not exist.', $input->getArgument('target'))
            );
        }

        if (!function_exists('symlink') && $input->getOption('symlink')) {
            throw new \InvalidArgumentException(
                'The symlink() function is not available on your system.' .
                'You need to install the assets without the --symlink option.'
            );
        }

        $kernel = $this->getContainer()->get('kernel');
        $kernelRootDir = $kernel->getRootDir();
        $rootDir = $this->getContainer()->getParameter('joomla.root_dir');

        // install normal roots
        $this->filesystem = $this->getContainer()->get('filesystem');
        $this->input = $input;
        $this->output = $output;

        $process = new Process('git --version');
        $process->run();
        if ($process->getExitCode() !== 0) {
            $output->writeln('<error>Failed to execute git --version</error>');

            return;
        } else {
            $output->writeln(sprintf('Found <comment>%s</comment>', trim($process->getOutput())));
        }

        $process = new Process('git status -z', $rootDir);
        $process->run();

        if ($process->getExitCode() !== 0) {
            $output->writeln('<error>Failed to execute git status -z</error>');

            return;
        }

        $unknownFiles = array_keys($this->parseStatusOutput($process->getOutput()));

        $knownFiles = [];

        foreach ($this->getContainer()->getParameter('joomla.installations') as $resources) {
            foreach ($resources as $resource) {
                $knownFiles[] = $resource['target'];
            }
        }

        foreach ($unknownFiles as $k => $unknownFile) {
            foreach (['tmp'] as $ignore) {
                if (preg_match('#^' . preg_quote($ignore . DIRECTORY_SEPARATOR, '#') . '#', $unknownFile)) {
                    $this->output->writeln(sprintf('Ignore <comment>%s</comment>', $unknownFile));
                    continue 2;
                }
            }
            foreach ($knownFiles as $knownFile) {
                $part = substr($unknownFile, 0, strlen($knownFile));
                if ($knownFile === $part) {
                    $this->output->writeln(
                        sprintf('<fg=green>Asset <comment>%s</comment> already known</fg=green>', $unknownFile)
                    );
                    unset($unknownFiles[$k]);
                }
            }
        }

        $config = [];

        foreach ($unknownFiles as $unknownFile) {
            $resourcePath = 'Resources' . DIRECTORY_SEPARATOR . 'joomla' . DIRECTORY_SEPARATOR . $unknownFile;
            $targetDir = $kernelRootDir . DIRECTORY_SEPARATOR . $resourcePath;

            $originDir = $targetArg . DIRECTORY_SEPARATOR . $unknownFile;

            $this->output->writeln(
                sprintf('Installing files for <comment>%s</comment> into <comment>%s</comment>', $originDir, $targetDir)
            );
            if ($force) {
                if (is_dir($originDir)) {
                    $this->filesystem->mkdir($targetDir, 0777);
                    $this->filesystem->mirror(
                        $originDir,
                        $targetDir,
                        Finder::create()->ignoreDotFiles(false)->in($originDir)
                    );
                } else {
                    $this->filesystem->mkdir(dirname($targetDir), 0777);
                    $this->filesystem->copy($originDir, $targetDir);
                }
            }

            $config[] = [
                'resource' => $resourcePath,
                'target'   => $unknownFile
            ];
        }

        if (empty($config)) {
            $this->output->writeln("<fg=green>Everything fine!</fg=green>");

            return;
        }

        $config = Yaml::dump(
            [
                'xtain_joomla' => [
                    'install' => [
                        'app' => $config
                    ]
                ]
            ],
            4
        );

        $this->output->writeln("Add this to config.yml:\n\n" . $config);
    }
}
