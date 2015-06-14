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

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface JoomlaInterface
 *
 * @package XTAIN\Bundle\JoomlaBundle\Joomla
 */
interface JoomlaInterface
{
    /**
     * @var string
     */
    const SITE = 'site';

    /**
     * @var string
     */
    const ADMINISTRATOR = 'administrator';

    /**
     * @var int
     */
    const STATE_FRESH = 0; // the Joomla instance is not initialized

    /**
     * @var int
     */
    const STATE_INIT = 1; // the Joomla instance has been initialized

    /**
     * @var int
     */
    const STATE_STATUS_DEFINED = 2; // the response status is known

    /**
     * @var int
     */
    const STATE_INNER_CONTENT = 3; // Joomla has generated the inner content

    /**
     * @var int
     */
    const STATE_RESPONSE = 4; // Joomla has generated the Response object

    /**
     * Initializes the Joomla core
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function initialize();

    /**
     * @param string $type
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setApplication($type = self::SITE);

    /**
     * The shutdown method only catches exit instruction from the Joomla code to rebuild the correct response
     *
     * @param int $level
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function shutdown($level);

    /**
     * Disables the response
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function disableResponse();

    /**
     * Return true if the current Joomla object contains a valid Response object
     *
     * @return bool
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function hasResponse();

    /**
     * @return bool
     * @throws InvalidStateMethodCallException
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function is403();

    /**
     * @return bool
     * @throws InvalidStateMethodCallException
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function is404();

    /**
     * @return bool
     * @throws InvalidStateMethodCallException
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function isOffline();

    /**
     * @return bool
     * @throws InvalidStateMethodCallException
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function isOnline();

    /**
     * @return bool
     * @throws InvalidStateMethodCallException
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function isFound();

    /**
     * Return true if the Joomla is correctly installed
     *
     * @return bool
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function isInstalled();

    /**
     * @return int
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getState();

    /**
     * This method builds the state of the current Joomla instance
     *
     * @param Request $request
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     *
     * @see menu_execute_active_handler function for more information
     */
    public function defineState(Request $request);

    /**
     * Decorates the inner content and renders the page
     *
     * @return void
     * @throws InvalidStateMethodCallException
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function render();

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getResponse();

    /**
     * @return \JApplicationCms
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getApplication();

    /**
     * @return \JDocument
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getDocument();
}
