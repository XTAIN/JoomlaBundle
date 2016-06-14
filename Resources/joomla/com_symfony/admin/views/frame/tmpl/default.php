<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @var \Twig_Environment $twig */
$twig = $this->getTwigEnvironment();
echo $twig->render("XTAINJoomlaBundle:Joomla/Component/Frame:default.html.twig", array(
    'frame_url' => $this->frameUrl
));