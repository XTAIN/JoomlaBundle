<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Admin;

class MenuItem
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var MenuLink
     */
    protected $link;

    /**
     * @var array
     */
    protected $children;

    /**
     * MenuItem constructor.
     *
     * @param string $name
     * @param MenuLink|null $link
     * @param array $children
     */
    public function __construct($name, MenuLink $link = null, $children = array())
    {
        $this->name = $name;
        $this->link = $link;
        $this->children = $children;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getChildren()
    {
        return $this->children;
    }
}