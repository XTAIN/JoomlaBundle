<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View to show a frame.
 *
 * @since  1.5
 */
class SymfonyViewFrame extends JViewLegacy
{
	protected $frameUrl;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$this->frameUrl = JFactory::getApplication()->input->get('url', array(), 'raw');

		$this->addToolbar();
		JHtml::_('jquery.framework');

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$title = JFactory::getApplication()->input->get('title');

		if (empty($title)) {
			$title = 'Symfony';
		}

		JToolbarHelper::title($title);
	}
}
