<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\Joomla\Filesystem;

/**
 * Class Path
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla\Filesystem\Path
 */
class Path extends \JProxy_JPath
{
    /**
     * @param mixed  $paths
     * @param string $file
     *
     * @return mixed
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function find($paths, $file)
    {
        // Force to array
        if (!is_array($paths) && !($paths instanceof \Iterator))
        {
            settype($paths, 'array');
        }

        // Start looping through the path set
        foreach ($paths as $path)
        {
            // Get the path to the file
            $fullname = $path . '/' . $file;

            // Is the path based on a stream?
            if (strpos($path, '://') === false)
            {
                // Not a stream, so do a realpath() to avoid directory
                // traversal attempts on the local file system.

                $pathFile = new \SplFileInfo($path);
                $fullnameFile = new \SplFileInfo($fullname);

                $path = $pathFile->getPathInfo()->getRealPath();
                $fullname = $fullnameFile->getPathInfo()->getRealPath();

                if ($path !== false) {
                    $path .= DIRECTORY_SEPARATOR . $pathFile->getFilename();
                }

                if ($fullname !== false) {
                    $fullname .= DIRECTORY_SEPARATOR . $fullnameFile->getFilename();
                }
            }

            /*
             * The substr() check added to make sure that the realpath()
             * results in a directory registered so that
             * non-registered directories are not accessible via directory
             * traversal attempts.
             */
            if (file_exists($fullname) && substr($fullname, 0, strlen($path)) == $path)
            {
                return $fullname;
            }
        }

        // Could not find the file in the set of paths
        return false;
    }
}