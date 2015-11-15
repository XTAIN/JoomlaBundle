<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Component\Module;

use XTAIN\Bundle\JoomlaBundle\Entity\Module;

/**
 * Interface ModuleInterface
 * @package XTAIN\Bundle\JoomlaBundle\Component\Module
 */
interface ModuleRendererInterface
{
    /**
     * @param Module $module
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setModule(Module $module);

    public function render();

    /**
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function renderSettings();
}