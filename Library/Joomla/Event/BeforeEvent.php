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
 * Class BeforeEvent
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla\Event
 */
class BeforeEvent extends Event
{
    /**
     * @var array|null
     */
    protected $args = [];

    /**
     * @param array|null $args
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function __construct($args = [])
    {
        $this->args = $args;
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
     * @param array|null $args
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setArguments($args)
    {
        $this->args = $args;
    }
}
