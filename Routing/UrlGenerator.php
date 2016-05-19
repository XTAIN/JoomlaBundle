<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Hans Mackowiak <hmackowiak@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Routing;

use Symfony\Component\Routing\Generator\UrlGenerator as BaseGenerator;

/**
 * Class UrlGenerator
 *
 * @author  Hans Mackowiak <hmackowiak@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Routing
 */
class UrlGenerator extends BaseGenerator
{
    /**
     * @var UrlPatcher
     */
    private $patcher;

    /**
     * @param UrlPatcher $patcher
     *
     * @author Hans Mackowiak <hmackowiak@xtain.net>
     * @return UrlGenerator
     */
    public function setPatcher($patcher)
    {
        $this->patcher = $patcher;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, array $requiredSchemes = array())
    {

        if (isset($this->patcher)) {
            if($this->patcher->overrideRouteTokens($name, $tokens)) {

            }
        }

        return parent::doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, $requiredSchemes);
    }

}