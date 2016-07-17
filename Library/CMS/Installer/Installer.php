<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\CMS\Installer;

use JFile;
use JFolder;
use JInstaller;
use JLog;
use JPath;
use JText;
use XTAIN\Bundle\JoomlaBundle\Installation\Asset;
use XTAIN\Bundle\JoomlaBundle\Joomla\ResourceLocator;

/**
 * @author Maximilian Ruta <mr@xtain.net>
 */
class Installer extends \JProxy_JInstaller
{
    /**
     * @var ResourceLocator
     */
    protected static $resourceLocator;

    /**
     * @var Asset
     */
    protected static $assetInstaller;

    /**
     * @param ResourceLocator $resourceLocator
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setResourceLocator(ResourceLocator $resourceLocator)
    {
        self::$resourceLocator = $resourceLocator;
    }

    /**
     * @param Asset $assetInstaller
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setAssetInstaller(Asset $assetInstaller)
    {
        self::$assetInstaller = $assetInstaller;
    }

    /**
     * Constructor
     *
     * @param   string  $basepath       Base Path of the adapters
     * @param   string  $classprefix    Class prefix of adapters
     * @param   string  $adapterfolder  Name of folder to append to base path
     *
     * @since   3.1
     */
    public function __construct($basepath = null, $classprefix = 'JInstallerAdapter', $adapterfolder = 'adapter')
    {
        if ($basepath === null) {
            $basepath = JPATH_LIBRARIES . '/cms/installer';
        }

        parent::__construct($basepath, $classprefix, $adapterfolder);
    }

    /**
     * Returns the global Installer object, only creating it if it doesn't already exist.
     *
     * @param   string  $basepath       Base Path of the adapters
     * @param   string  $classprefix    Class prefix of adapters
     * @param   string  $adapterfolder  Name of folder to append to base path
     *
     * @return  JInstaller  An installer object
     *
     * @since   3.1
     */
    public static function getInstance($basepath = null, $classprefix = 'JInstallerAdapter', $adapterfolder = 'adapter')
    {
        if ($basepath === null) {
            $basepath = JPATH_LIBRARIES . '/cms/installer';
        }

        return parent::getInstance($basepath, $classprefix, $adapterfolder);
    }

    /**
     * @param string $path
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function relativeJoomlaTargetPath($path)
    {
        if (strpos($path, JPATH_ROOT) === 0) {
            return substr($path, strlen(JPATH_ROOT) + 1);
        }

        return $path;
    }

    /**
     * @param string $path
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function rewriteTargetPath($path)
    {
        if (strpos($path, JPATH_ROOT) === 0) {
            $path = self::$resourceLocator->rewrite(
                $this->relativeJoomlaTargetPath($path)
            );
        }

        return $path;
    }

    /**
     * @param string $path
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function relativeTargetPath($path)
    {
        if (strpos($path, JPATH_ROOT) === 0) {
            $path = self::$resourceLocator->rewrite(
                $this->relativeJoomlaTargetPath($path),
                true
            );
        }

        return $path;
    }

    /**
     * Copyfiles
     *
     * Copy files from source directory to the target directory
     *
     * @param   array    $files      Array with filenames
     * @param   boolean  $overwrite  True if existing files can be replaced
     *
     * @return  boolean  True on success
     *
     * @since   3.1
     */
    public function copyFiles($files, $overwrite = null)
    {
        /*
         * To allow for manual override on the overwriting flag, we check to see if
         * the $overwrite flag was set and is a boolean value.  If not, use the object
         * allowOverwrite flag.
         */

        if (is_null($overwrite) || !is_bool($overwrite))
        {
            $overwrite = $this->overwrite;
        }

        $config = array();

        /*
         * $files must be an array of filenames.  Verify that it is an array with
         * at least one file to copy.
         */
        if (is_array($files) && count($files) > 0)
        {
            foreach ($files as $file)
            {
                // Get the source and destination paths
                $filesource = JPath::clean($file['src']);
                $filedest = JPath::clean($file['dest']);
                $filetype = array_key_exists('type', $file) ? $file['type'] : 'file';

                $config[$this->relativeTargetPath($filedest)] = $this->relativeJoomlaTargetPath($filedest);

                $filedest = $this->rewriteTargetPath($filedest);

                if (!file_exists(dirname($filedest))) {
                    mkdir(dirname($filedest), 0777, true);
                }

                if (!file_exists($filesource))
                {
                    /*
                     * The source file does not exist.  Nothing to copy so set an error
                     * and return false.
                     */
                    JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_NO_FILE', $filesource), JLog::WARNING, 'jerror');

                    return false;
                }
                elseif (($exists = file_exists($filedest)) && !$overwrite)
                {
                    // It's okay if the manifest already exists
                    if ($this->getPath('manifest') == $filesource)
                    {
                        continue;
                    }

                    // The destination file already exists and the overwrite flag is false.
                    // Set an error and return false.
                    JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_FILE_EXISTS', $filedest), JLog::WARNING, 'jerror');

                    return false;
                }
                else
                {
                    // Copy the folder or file to the new location.
                    if ($filetype == 'folder')
                    {
                        if (!(JFolder::copy($filesource, $filedest, null, $overwrite)))
                        {
                            JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_FAIL_COPY_FOLDER', $filesource, $filedest), JLog::WARNING, 'jerror');

                            return false;
                        }

                        $step = array('type' => 'folder', 'path' => $filedest);
                    }
                    else
                    {
                        if (!(JFile::copy($filesource, $filedest, null)))
                        {
                            JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_FAIL_COPY_FILE', $filesource, $filedest), JLog::WARNING, 'jerror');

                            // In 3.2, TinyMCE language handling changed.  Display a special notice in case an older language pack is installed.
                            if (strpos($filedest, 'media/editors/tinymce/jscripts/tiny_mce/langs'))
                            {
                                JLog::add(JText::_('JLIB_INSTALLER_NOT_ERROR'), JLog::WARNING, 'jerror');
                            }

                            return false;
                        }

                        $step = array('type' => 'file', 'path' => $filedest);
                    }

                    /*
                     * Since we copied a file/folder, we want to add it to the installation step stack so that
                     * in case we have to roll back the installation we can remove the files copied.
                     */
                    if (!$exists)
                    {
                        $this->stepStack[] = $step;
                    }
                }
            }
        }
        else
        {
            // The $files variable was either not an array or an empty array
            return false;
        }

        self::$assetInstaller->addToConfig($config);
        self::$assetInstaller->install();

        return count($files);
    }

}