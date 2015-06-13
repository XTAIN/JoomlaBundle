<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Joomla;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use XTAIN\Bundle\JoomlaBundle\Library\Config;

/**
 * Class PrefixSubscriber
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Joomla
 */
class PrefixSubscriber implements \Doctrine\Common\EventSubscriber
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }


    /**
     * @return array
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getSubscribedEvents()
    {
        return [
            'loadClassMetadata'
        ];
    }

    /**
     * @param LoadClassMetadataEventArgs $args
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $classMetadata = $args->getClassMetadata();

        if (!($classMetadata instanceof \Doctrine\ORM\Mapping\ClassMetadataInfo)) {
            return;
        }

        // Do not re-apply the prefix in an inheritance hierarchy.
        if ($classMetadata->isInheritanceTypeSingleTable() && !$classMetadata->isRootEntity()) {
            return;
        }

        $prefix = $this->config->getDbPrefix();

        if (strpos($classMetadata->namespace, 'XTAIN\Bundle\JoomlaBundle') === 0) {
            $classMetadata->setPrimaryTable([
                'name' => $prefix . $classMetadata->getTableName()
            ]);

            foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
                if ($mapping['type'] == \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY
                    && isset($classMetadata->associationMappings[$fieldName]['joinTable']['name'])) {
                    $mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];
                    $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $prefix . $mappedTableName;
                }
            }
        }
    }
}