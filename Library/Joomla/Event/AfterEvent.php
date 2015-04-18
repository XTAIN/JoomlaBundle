<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\Joomla\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class AfterEvent
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla\Event
 */
class AfterEvent extends Event
{
    /**
     * @var array|null
     */
    protected $args = [];

    /**
     * @var array
     */
    protected $result = [];

    /**
     * @param array|null $args
     * @param array      $result
     */
    public function __construct($args = [], array $result = [])
    {
        $this->args = $args;
        $this->result = $result;
    }

    /**
     * @return array|null
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getArguments()
    {
        return $this->args;
    }

    /**
     * @return array
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param array $result
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setResult(array $result = null)
    {
        $this->result = $result;
    }
}
