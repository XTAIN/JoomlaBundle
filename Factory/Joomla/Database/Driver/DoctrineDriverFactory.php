<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Database\Driver;

use Doctrine\ORM\EntityManagerInterface;
use XTAIN\Bundle\JoomlaBundle\Factory\DependencyFactoryInterface;
use XTAIN\Bundle\JoomlaBundle\Library\Joomla\Database\Driver\AbstractDoctrineDriver;

/**
 * Class DoctrineDriverFactory
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Database\Driver
 */
class DoctrineDriverFactory implements DependencyFactoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var string[]
     */
    protected $drivers = [];

    /**
     * @param EntityManagerInterface $entityManager
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $class
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function addDatabasePlatform($class)
    {
        $this->drivers[] = $class;
    }

    /**
     * @return AbstractDoctrineDriver
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function findMatchingPlatform()
    {
        $platform = $this->entityManager->getConnection()->getDatabasePlatform();

        foreach ($this->drivers as $class) {
            if ($class::supportsPlatform($platform)) {
                return $class;
            }
        }

        throw new \RuntimeException(sprintf('Unsupported Database Platform %s', get_class($platform)));
    }

    /**
     * @return AbstractDoctrineDriver
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getInstance()
    {
        $platform = $this->findMatchingPlatform();

        \XTAIN\Bundle\JoomlaBundle\Library\Loader::registerAlias('JDatabaseDriverDoctrine', $platform);

        $this->injectStaticDependencies();

        return \JFactory::getDbo();
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        AbstractDoctrineDriver::setEntityManager($this->entityManager);
    }
}
