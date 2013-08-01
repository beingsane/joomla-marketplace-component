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
	 * Return download url from tmpl update manifest
	 * 
	 * @param int $eid
     *
     * @return  mixed  The string package url otherwise false
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

    /**
     * Return extension item
     *
     * @since   3.1
     */
    public function getItem()
    {
        if (empty($this->item)) {
            $db     = JFactory::getDBO();
            $query  = $db->getQuery(true);

            $extension_id = JFactory::getApplication()->input->getInt('id');

            // Select the required fields from the updates table
            $query->select('a.marketplace_extension_id, a.name, a.type, a.buttonurl, a.authorurl, a.infourl, a.element, a.type, a.collection, a.display, a.author, a.image, a.plan, a.reviews, a.rating, a.category, a.description');
            $query->from($db->quoteName('#__marketplace_extensions').' AS a');
            // Join installed extensions
            $query->select('e.extension_id');
            $query->join('LEFT', $db->quoteName('#__extensions').' AS e ON e.element = a.element');
            $query->where('a.marketplace_extension_id='.$db->quote($extension_id));
            $db->setQuery($query);
            $this->item = $db->loadObject();
        }

        return $this->item;
    }
}