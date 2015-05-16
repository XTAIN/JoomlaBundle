<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Component\Menu\View;

use Symfony\Component\Routing\RouterInterface;

if (!class_exists('JAdminCssMenu'))
{
    require JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'mod_menu' . DIRECTORY_SEPARATOR . 'menu.php';
}

/**
 * Class AdminMenu
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Component\Menu\View\AdminMenu
 */
class AdminMenu extends \JAdminCSSMenu
{
    /**
     * @var RouterInterface
     */
    protected static $router;

    /**
     * @param RouterInterface $router
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setRouter(RouterInterface $router)
    {
        self::$router = $router;
    }

    /**
     * @param \JMenuNode $root
     */
    public function __construct(\JMenuNode $root)
    {
        \XTAIN\Bundle\JoomlaBundle\Library\Loader::injectStaticDependencies(__CLASS__);

        parent::__construct();

        $this->_root = $root;
        $this->_current = & $this->_root;
    }

    protected function route($link)
    {
        $admin = self::$router->generate('joomla_administrator');

        return $admin . $link;
    }

    public function renderLevel($depth, $childDeep = 0)
    {
        // Build the CSS class suffix
        $class = '';

        if ($this->_current->hasChildren())
        {
            $class = ' class="treeview"';
        }

        if ($this->_current->class == 'separator')
        {
            return;
        }

        if ($this->_current->hasChildren() && $this->_current->class)
        {
            $class = ' class="treeview"';
        }

        if ($this->_current->class == 'disabled')
        {
            $class = ' class="treeview disabled"';
        }

        $icon = '';
        if ($childDeep > 0) {
            $class = '';
            $icon = '<i class="fa fa-angle-double-right"></i>';
        }

        // Print the item
        echo "<li" . $class . ">";

        // Print a link if it exists
        $linkClass = array();
        $dataToggle = '';
        $dropdownCaret = '';

        if ($this->_current->hasChildren())
        {
            $linkClass[] = '';
            $dataToggle = '';

            if (!$this->_current->getParent()->hasParent())
            {
                $dropdownCaret = ' <span class="caret"></span>';
            }
        }

        if ($this->_current->link != null && $this->_current->getParent()->title != 'ROOT')
        {
            $iconClass = $this->getIconClass($this->_current->class);

            if (!empty($iconClass))
            {
                $linkClass[] = $iconClass;
            }
        }

        // Implode out $linkClass for rendering
        $linkClass = ' class="' . implode(' ', $linkClass) . '"';

        if ($this->_current->link != null && $this->_current->target != null)
        {
            echo "<a" . $linkClass . " " . $dataToggle . " href=\"" . $this->route($this->_current->link) . "\" target=\"" . $this->_current->target . "\" >" . $icon
                . $this->_current->title . $dropdownCaret . "</a>";
        }
        elseif ($this->_current->link != null && $this->_current->target == null)
        {
            echo "<a" . $linkClass . " " . $dataToggle . " href=\"" . $this->route($this->_current->link) . "\">"  . $icon . $this->_current->title . $dropdownCaret . "</a>";
        }
        elseif ($this->_current->title != null)
        {
            echo "<a" . $linkClass . " " . $dataToggle . ">"  . $icon . $this->route($this->_current->title) . $dropdownCaret . "</a>";
        }
        else
        {
            echo "<span></span>";
        }

        // Recurse through children if they exist
        $childDeep++;
        while ($this->_current->hasChildren())
        {
            if ($this->_current->class)
            {
                $id = '';

                if (!empty($this->_current->id))
                {
                    $id = ' id="menu-' . strtolower($this->_current->id) . '"';
                }

                echo '<ul' . $id . ' class="treeview-menu menu-component">' . "\n";
            }
            else
            {
                echo '<ul class="treeview-menu">' . "\n";
            }

            foreach ($this->_current->getChildren() as $child)
            {
                $this->_current = & $child;
                $this->renderLevel($depth++, $childDeep);
            }

            echo "</ul>\n";
        }

        echo "</li>\n";
    }
}
