<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Admin;

interface MenuInterface
{
    /**
     * @return MenuItem[]
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getItems();
}