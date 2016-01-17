<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\Joomla\Application;

use Symfony\Component\DependencyInjection\ContainerInterface;
use XTAIN\Bundle\JoomlaBundle\Joomla\ApplicationClosedException;
use XTAIN\Bundle\JoomlaBundle\Joomla\ApplicationInterface;

/**
 * Class Base
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla\Application
 */
abstract class Base extends \JProxy_JApplicationBase implements ApplicationInterface
{
    /**
     * @var ContainerInterface
     */
    protected static $container;

    /**
     * @param ContainerInterface $container
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setContainer(ContainerInterface $container = null)
    {
        self::$container = $container;
    }

    /**
     * @return ContainerInterface
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getContainer()
    {
        return self::$container;
    }

    /**
     * @param int $code
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function close($code = 0)
    {
        throw new ApplicationClosedException($code);
    }

    /**
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function symfonyInitialise()
    {

    }
}
