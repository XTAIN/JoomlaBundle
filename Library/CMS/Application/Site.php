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

/**
 * Class Site
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\CMS\Application
 */
class Site extends \JProxy_JApplicationSite
{
    /**
     * @param null $component
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function dispatch($component = null)
    {
        // Mark beforeDispatch in the profiler.
        JDEBUG ? $this->profiler->mark('beforeDispatch') : null;

        parent::dispatch($component);
    }

    protected function initialiseApp($options = [])
    {
        // Mark beforeInitialise in the profiler.
        JDEBUG ? $this->profiler->mark('beforeInitialise') : null;

        parent::initialiseApp($options);
    }

    protected function route()
    {
        // Mark beforeRoute in the profiler.
        JDEBUG ? $this->profiler->mark('beforeRoute') : null;

        parent::route();
    }

    protected function render()
    {
        // Mark beforeRender in the profiler.
        JDEBUG ? $this->profiler->mark('beforeRender') : null;

        parent::render();
    }
}
