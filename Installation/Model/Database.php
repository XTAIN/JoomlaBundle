<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Installation\Model;

use InstallationApplicationWeb;
use InstallationHelperDatabase;
use InstallationModelDatabase;
use JArrayHelper;
use JDatabaseDriver;
use JFactory;
use JLanguage;
use JText;
use RuntimeException;

/**
 * Class Database
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Installation\Model
 */
class Database extends InstallationModelDatabase
{

    /**
     * Method to initialise the database.
     *
     * @param   array  $options  The options to use for configuration.
     *
     * @return  JDatabaseDriver|boolean  Database object on success, boolean false on failure
     *
     * @since   3.1
     */
    public function initialise($options)
    {
        // Get the application.
        /* @var InstallationApplicationWeb $app */
        $app = JFactory::getApplication();

        // Get the options as a object for easier handling.
        $options = JArrayHelper::toObject($options);

        // Load the back-end language files so that the DB error messages work.
        $lang = JFactory::getLanguage();
        $currentLang = $lang->getTag();

        // Load the selected language
        if (JLanguage::exists($currentLang, JPATH_ADMINISTRATOR))
        {
            $lang->load('joomla', JPATH_ADMINISTRATOR, $currentLang, true);
        }
        // Pre-load en-GB in case the chosen language files do not exist.
        else
        {
            $lang->load('joomla', JPATH_ADMINISTRATOR, 'en-GB', true);
        }

        // Ensure a database type was selected.
        if (empty($options->db_type))
        {
            $app->enqueueMessage(JText::_('INSTL_DATABASE_INVALID_TYPE'), 'notice');

            return false;
        }

        // Get a database object.
        try
        {
            return InstallationHelperDatabase::getDbo(
                $options->db_type, $options->db_host, $options->db_user, $options->db_pass, $options->db_name, $options->db_prefix, $options->db_select
            );
        }
        catch (RuntimeException $e)
        {
            $app->enqueueMessage(JText::sprintf('INSTL_DATABASE_COULD_NOT_CONNECT', $e->getMessage()), 'notice');

            return false;
        }
    }
}