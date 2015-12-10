<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Joomla;


class JoomlaHelper
{
    /**
     * @param string $string
     * @param array  $replace
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function trans($string, $replace = [])
    {
        $text = \JText::_($string);

        foreach ($replace as $key => $value) {
            $text = str_replace($key, $value, $text);
        }

        return $text;
    }

    /**
     * @param string $item
     *
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function menuLink($item)
    {
        $app = \JFactory::getApplication();
        $menu = $app->getMenu();
        $item = $menu->getItem($item);

        return \JRoute::_($item->link . '&Itemid=' . $item->id);
    }
}