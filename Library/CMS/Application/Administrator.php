<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\CMS\Application;

use JApplicationWebClient;
use JInput;
use Joomla\Registry\Registry;
use XTAIN\Bundle\JoomlaBundle\Library\Joomla\Uri\Uri;

/**
 * Class Administrator
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\CMS\Application
 */
class Administrator extends \JProxy_JApplicationAdministrator
{
    /**
     * @var string
     */
    const ROUTE = 'joomla_administrator';

    public function __construct(JInput $input = null, Registry $config = null, JApplicationWebClient $client = null)
    {
        parent::__construct($input, $config, $client);

        $route = Uri::getAdministratorRoute();
    }
}
