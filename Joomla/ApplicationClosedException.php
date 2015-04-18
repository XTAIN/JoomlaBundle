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

use Exception;

/**
 * Class ApplicationClosedException
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Joomla
 */
class ApplicationClosedException extends \RuntimeException
{
    /**
     * @param int       $code
     * @param string    $message
     * @param Exception $previous
     */
    public function __construct($code = 0, $message = "", Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
