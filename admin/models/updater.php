<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Import library dependencies
jimport('joomla.application.component.modellist');

/**
 * Updater Marketplace Model
 *
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @since       3.1
 */
class MarketplaceModelUpdater extends JModelLegacy
{
	/**
	 * List published repositories
	 * 
	 * @since	3.1
	 */
	public function getRepositories()
	{
		if (empty($this->repositories)) {
			$db     = JFactory::getDBO();
			$query  = $db->getQuery(true);
	
			// Select the required fields from the updates table
			$query->select('a.name, a.location');
	
			$query->from($db->quoteName('#__marketplace_repositories').' AS a');
			$query->where('a.published>=1');
			
			$db->setQuery($query);
			$this->repositories = $db->loadAssocList();
		}
		
		return $this->repositories;
	}
}