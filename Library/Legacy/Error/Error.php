<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\Legacy\Error;

/**
 * Class Error
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Legacy\Error
 */
class Error extends \JProxy_JError
{
    /**
     * @param object $exception
     *
     * @return \reference
     * @throws \Exception
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function throwError(&$exception)
    {
        if ($exception instanceof \Exception) {
            throw $exception;
        }

        return parent::throwError($exception);
    }

    public static function attachHandler()
    {
    }

    public static function detachHandler()
    {
    }
}
