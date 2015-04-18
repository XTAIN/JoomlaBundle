<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Routing;

use Psr\Log\LoggerAwareInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;

/**
 * Interface RouterInterface
 */
interface RouterInterface extends \Symfony\Component\Routing\RouterInterface, LoggerAwareInterface
{
}
