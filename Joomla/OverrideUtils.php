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

/**
 * Class OverrideUtils
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Joomla
 */
class OverrideUtils
{
    /**
     * @param string $file
     * @param string $oldName
     * @param string $newName
     * @param bool   $static
     *
     * @return mixed|string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function classReplace($file, $oldName, $newName, $static = false)
    {
        $code = file_get_contents($file);
        $code = str_replace('<?php', '', $code);
        $code = str_replace('?>', '', $code);
        $code = preg_replace('/(final[\s]+)?class[\s]+' . preg_quote($oldName) . '/i', 'class ' . $newName, $code);

        if ($static !== false && $static !== 'self') {
            // TODO this is a ugly hack!
            $code = str_replace('static::', $static . '::', $code);
            $code = str_replace('self::', $static . '::', $code);
        }

        return $code;
    }
}