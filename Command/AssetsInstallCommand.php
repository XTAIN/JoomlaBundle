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

/**
 * Command that places bundle web assets into a given directory.
 *
 * @author Maximilian Ruta <mr@xtain.net>
 */
class AssetsInstallCommand extends ContainerAwareCommand
{
    const MODE_RELATIVE = 'relative';

    const MODE_SYMLINK = 'symlink';

    const MODE_COPY = 'copy';

    /**
     * @var string
     */
    protected $mode;

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
            ->setName('xtain:joomla:assets:install')
            ->setDefinition(
                [
                    new InputArgument('target', InputArgument::OPTIONAL, 'The target directory', 'web'),
                ]
            )
            ->addOption('mode', null, InputOption::VALUE_OPTIONAL, 'copy|symlink|relative', self::MODE_RELATIVE)
            ->setDescription('Installs bundles web assets under a public web directory')
            ->setHelp(
                <<<EOT
                The <info>%command.name%</info> command installs joomla's assets into a given
directory (e.g. the web directory).

<info>php %command.full_name% web</info>

The basic joomla structure will be created inside target directory by creating symlinks to the vendor directory.
EOT
            );
    }

    /**
     * @param string $originDir
     * @param string $targetDir
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function syncDir($originDir, $targetDir)
    {
        echo $targetDir;
        if (is_dir($originDir)) {
            $this->output->writeln(
                sprintf('Installing files for <comment>%s</comment> into <comment>%s</comment>', $originDir, $targetDir)
            );

            $this->filesystem->remove($targetDir);

            if ($this->mode != self::MODE_COPY) {
                if ($this->mode == self::MODE_RELATIVE) {
                    $relativeOriginDir = $this->filesystem->makePathRelative($originDir, realpath(dirname($targetDir)));
                } else {
                    $relativeOriginDir = $originDir;
                }
                $relativeOriginDir = rtrim($relativeOriginDir, DIRECTORY_SEPARATOR);
                $this->filesystem->symlink($relativeOriginDir, $targetDir);
            } else {
                if (!is_dir($targetDir)) {
                    $this->filesystem->mkdir($targetDir, 0777);
                }
                $this->filesystem->mirror(
                    $originDir,
                    $targetDir,
                    Finder::create()->ignoreDotFiles(false)->in($originDir)
                );
            }
        } else {
            $this->output->writeln(
                sprintf('Installing file <comment>%s</comment> into <comment>%s</comment>', $originDir, $targetDir)
            );

            $this->filesystem->remove($targetDir);

            if ($this->mode != self::MODE_COPY) {
                if ($this->mode == self::MODE_RELATIVE) {
                    $relativeOriginDir =
                        $this->filesystem->makePathRelative(dirname($originDir), realpath(dirname($targetDir))) .
                        DIRECTORY_SEPARATOR .
                        basename($originDir);
                } else {
                    $relativeOriginDir = $originDir;
                }
                $this->filesystem->symlink($relativeOriginDir, $targetDir);
            } else {
                if (!is_dir($targetDir)) {
                    $this->filesystem->mkdir(dirname($targetDir), 0777);
                }
                $this->filesystem->copy($originDir, $targetDir);
            }
        }
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
        $this->mode = $input->getOption('mode');

        $targetArg = rtrim($input->getArgument('target'), '/\\');

        if (!is_dir($targetArg)) {
            throw new \InvalidArgumentException(
                sprintf('The target directory "%s" does not exist.', $input->getArgument('target'))
            );
        }

        if (!function_exists('symlink') && $input->getOption('symlink')) {
            throw new \InvalidArgumentException(
                'The symlink() function is not available on your system. ' .
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

        $output->writeln('Cleanup web path');

        $finder = new Finder();
        $finder->followLinks()->filter(
            function (SplFileInfo $file) {
                return $file->isLink();
            }
        )->in($targetArg);

        $links = [];

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $output->writeln(sprintf('Found link <comment>%s</comment>', $file->getPathname()));
            $links[] = $file->getPathname();
        }

        if (!empty($links)) {
            $output->writeln('Sort links');

            usort(
                $links,
                function ($a, $b) {
                    return strlen($b) - strlen($a);
                }
            );

            foreach ($links as $link) {
                $output->writeln(sprintf('Remove link <comment>%s</comment>', $link));
                $this->filesystem->remove($link);
            }

            clearstatcache();
        }

        $output->writeln(sprintf('Installing joomla as <comment>%s</comment>', $this->mode));

        foreach ($this->getContainer()->get('joomla')->getAssetRoots() as $assetRoot) {
            if (dirname($assetRoot) != '.') {
                if (!is_dir($targetArg . DIRECTORY_SEPARATOR . dirname($assetRoot))) {
                    $this->filesystem->mkdir($targetArg . DIRECTORY_SEPARATOR . dirname($assetRoot));
                }
            }
            $this->syncDir($rootDir . DIRECTORY_SEPARATOR . $assetRoot, $targetArg . DIRECTORY_SEPARATOR . $assetRoot);
        }

        // install assets
        $output->writeln(sprintf('Installing custom assets as <comment>%s</comment>', $this->mode));

        foreach ($this->getContainer()->getParameter('joomla.installations') as $name => $installations) {
            $output->writeln(sprintf('Installing asset <comment>%s</comment>', $name));
            foreach ($installations as $installation) {
                if (substr($installation['resource'], 0, 1) == '@') {
                    $path = $kernel->locateResource($installation['resource']);
                } else {
                    $path = $kernelRootDir . DIRECTORY_SEPARATOR . $installation['resource'];
                }
                if (!$this->filesystem->exists($path)) {
                    throw new \RuntimeException(sprintf('File %s not found', $path));
                }
                $this->syncDir($path, $targetArg . DIRECTORY_SEPARATOR . $installation['target']);
            }
        }
    }
}
