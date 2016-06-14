<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Admin;

class MenuLink
{
    /**
     * @var string
     */
    protected $link;

    /**
     * @var bool
     */
    protected $framed = false;

    /**
     * MenuLink constructor.
     *
     * @param string $link
     * @param bool $framed
     */
    public function __construct($link, $framed = false)
    {
        $this->link = $link;
        $this->framed = $framed;
    }

    /**
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return bool
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function isFramed()
    {
        return $this->framed;
    }
}