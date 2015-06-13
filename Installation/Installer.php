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

use Doctrine\Common\Util\Debug;
use InstallationApplicationWeb;
use XTAIN\Bundle\JoomlaBundle\Installation\Model\Configuration as ConfigurationModel;
use XTAIN\Bundle\JoomlaBundle\Installation\Model\Database as DatabaseModel;
use XTAIN\Bundle\JoomlaBundle\Joomla\JoomlaInterface;
use XTAIN\Bundle\JoomlaBundle\Library\Config;
use XTAIN\Bundle\JoomlaBundle\Library\Joomla\Factory;

/**
 * Class Installer
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Installation
 */
class Installer implements InstallerInterface
{
    /**
     * @var JoomlaInterface
     */
    protected $joomla;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param JoomlaInterface $joomla
     */
    public function __construct(JoomlaInterface $joomla, Config $config)
    {
        $this->joomla = $joomla;
        $this->config = $config;
    }

    /**
     * @param Configuration $configuration
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function install(Configuration $configuration)
    {
        // Register the Installation application
        \JLoader::registerPrefix('Installation', JPATH_INSTALLATION);

        $this->joomla->initialize();

        $app = new InstallationApplicationWeb();
        Factory::pushApplication($app);

        // Get the database model.
        $db = new DatabaseModel();

        $dboptions = [
            'db_type'   => 'doctrine',
            'db_host'   => $this->config->get('host'),
            'db_user'   => $this->config->get('user'),
            'db_pass'   => $this->config->get('password'),
            'db_name'   => $this->config->get('db'),
            'db_prefix' => $this->config->get('dbprefix'),
            'db_select' => true
        ];

        $driver = $db->initialise($dboptions);

        $pwd = getcwd();
        chdir(JPATH_INSTALLATION);

        $db->createTables(array_merge($dboptions, [
            'language'   => 'en-GB',
            'db_created' => true,
            'db_type'    => $driver->name
        ]));

        $configration = new ConfigurationModel();

        $configration->setup(array_merge($dboptions, [
            'site_offline'   => 0,
            'site_name'      => '',
            'site_metadesc'  => '',
            'helpurl'        => '',
            'admin_user'     => $configuration->getUsername(),
            'admin_email'    => $configuration->getEmail(),
            'admin_password' => $configuration->getPassword()
        ]));

        chdir($pwd);

        Factory::popApplication();
    }
}