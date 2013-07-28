<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.updater.update');

/**
 * Extension Marketplace Model
 *
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @since       3.1
 */
class MarketplaceModelExtension extends JModelLegacy
{
	/**
	 * Return download url from extension update manifest
	 * 
	 * @param int $eid
	 * 
	 * @since	3.1
	 */
	public function getLink($extension_id)
	{
		$extension = $this->getTable('marketplaceextensions','jtable');
		$extension->load($extension_id);
		
		if ($extension->store_extension_id == 0 || $extension->plan != 'install' || empty($extension->url)) {
			return false;
		}
		
		$update = new JUpdate;
		$update->loadFromXML($extension->url);
		$package_url = $update->get('downloadurl', false);
		if (!$package_url) {
			return false;
		} else {
			$package_url = trim($package_url->_data);
		}
		
		return $package_url;
	}
}