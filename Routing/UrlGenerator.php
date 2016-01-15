<?php
/**
 * Created by IntelliJ IDEA.
 * User: hanmac
 * Date: 14.01.16
 * Time: 11:07
 */

namespace XTAIN\Bundle\JoomlaBundle\Routing;

use Symfony\Component\Routing\Generator\UrlGenerator as BaseGenerator;

class UrlGenerator extends BaseGenerator
{
    /**
     * @var JoomlaUrlPatcher
     */
    private $patcher;

    /**
     * @param JoomlaUrlPatcher $patcher
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
            $menu = $this->patcher->getRouteByName($name);

            if (isset($menu)) {
                return \JRoute::_($menu->getPath(), false);
            }
        }

        return parent::doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, $requiredSchemes);
    }

}