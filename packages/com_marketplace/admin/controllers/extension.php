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
 * Extension Store Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @since       3.1
 */
class MarketplaceControllerExtension extends JControllerLegacy
{
	/**
	 * Prepare to install by url
	 *
	 * @return  void
	 * 
	 * @since	3.1
	 */
	public function install()
	{
		// Check for request forgeries
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		
		$model = $this->getModel('extension');
		$extension_id = $this->input->getInt('eid');
		$url = $model->getLink($extension_id);
		
		if (!$url) {
			$url = 'index.php?option=com_marketplace';
		} else {

		}
	}
}