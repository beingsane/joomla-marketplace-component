<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Extensions Store Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @since       3.1
 */
class MarketplaceControllerMarketplace extends JControllerLegacy
{
	/**
	 * Finds extensions
	 *
	 * @return  void
	 * 
	 * @since	3.1
	 */
	public function findExtensions()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$document = JFactory::getDocument();
		
		$viewType = $document->getType();
		$viewName = 'updater';
		$viewLayout = $this->input->get('layout', 'default');
		
		$view  = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath, 'layout' => $viewLayout));
		$view->display();
	}
}
