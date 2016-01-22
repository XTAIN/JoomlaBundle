<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Hans Mackwowiak <hmackowiak@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\Joomla\Language;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

use RuntimeException;

/**
 * Class Language
 *
 * @author  Hans Mackwowiak <hmackowiak@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla\Language
 */
class Language extends \JProxy_JLanguage
{

    /**
     * Returns a list of known languages for an area
     *
     * @param   string  $basePath  The basepath to use
     *
     * @return  array  key/value pair with the language file and real name.
     *
     * @since   11.1
     */
    public static function getKnownLanguages($basePath = JPATH_BASE)
    {
        $dir = self::getLanguagePath($basePath);
        $knownLanguages = self::parseLanguageFiles($dir);
        return $knownLanguages;
    }

    /**
     * Searches for language directories within a certain base dir.
     *
     * @param   string  $dir  directory of files.
     *
     * @return  array  Array holding the found languages as filename => real name pairs.
     *
     * @since   11.1
     */
    public static function parseLanguageFiles($dir = null)
    {
        $languages = array();

        // add search for symlinks too
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::FOLLOW_SYMLINKS));

        foreach ($iterator as $file)
        {
            $langs    = array();
            $fileName = $file->getFilename();

            if (!$file->isFile() || !preg_match("/^([-_A-Za-z]*)\.xml$/", $fileName))
            {
                continue;
            }

            try
            {
                $metadata = self::parseXMLLanguageFile($file->getRealPath());

                if ($metadata)
                {
                    $lang = str_replace('.xml', '', $fileName);
                    $langs[$lang] = $metadata;
                }

                $languages = array_merge($languages, $langs);
            }
            catch (RuntimeException $e)
            {
            }
        }

        return $languages;
    }
}