<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Installation;

use Doctrine\ORM\EntityManagerInterface;
use XTAIN\Bundle\JoomlaBundle\Entity\Extension;
use XTAIN\Bundle\JoomlaBundle\Entity\ExtensionRepositoryInterface;

/**
 * Class ExtensionInstaller
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Installation
 */
class ExtensionInstaller implements ExtensionInstallerInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $enityManager;

    /**
     * @var ExtensionRepositoryInterface
     */
    protected $extensionRepository;

    /**
     * @param EntityManagerInterface       $entityManager
     * @param ExtensionRepositoryInterface $extensionRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ExtensionRepositoryInterface $extensionRepository
    ) {
        $this->enityManager = $entityManager;
        $this->extensionRepository = $extensionRepository;
    }

    /**
     * @param string $name
     *
     * @return bool
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function isExtensionInstalled($name)
    {
        return $this->extensionRepository->findByName($name) !== null;
    }

    /**
     * @param string $name
     * @param string $type
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function registerExtension($name, $type = 'component', $client = self::CLIENT_SITE)
    {
        $extension = $this->extensionRepository->findByName($name);

        if ($extension === null) {
            $extension = new Extension();
            $extension->setName($name);
            $extension->setElement($name);
            $extension->setType($type);
            $extension->setClientId($client);
            $extension->setManifestCache([
                'name' => $name,
                'type' => $type
            ]);
        }

        $extension->setEnabled(true);

        $this->enityManager->persist($extension);
        $this->enityManager->flush($extension);
    }
}