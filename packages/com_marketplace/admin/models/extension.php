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

        // ping item url to check content type before try to update using update manifest
        $http = new JHttp;
        $response = $http->get($extension->item_url);
        $is_xml = (strpos($response->headers['Content-Type'],'xml') === false) ? false : true ;
		
		if ($extension->marketplace_extension_id == 0 || $extension->plan != 'install' || empty($extension->item_url) || (substr($extension->item_url,-5) == '.html') || !$is_xml) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_MARKETPLACE_PROVIDE_UPDATE_MANIFEST_URL'),'warning');
			return false;
		}

        $update = new JUpdate;
        $update->loadFromXML($extension->item_url);
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
            $query->select('a.marketplace_extension_id, a.name, a.icon, a.thumbnail, a.tags, a.pathway, a.plan,a.author, a.description, a.gallery, a.version, a.reviews, a.rating, a.item_url, a.author_url, a.details_url, a.demo_url');
            $query->from($db->quoteName('#__marketplace_extensions').' AS a');
            // Join installed extensions
            $query->select('e.extension_id');
            $query->join('LEFT', $db->quoteName('#__extensions').' AS e ON e.element = a.identifier');
            // Join updates extensions
            $query->select('u.update_id');
            $query->join('LEFT', $db->quoteName('#__updates').' AS u ON (u.element = a.identifier AND e.extension_id = u.extension_id)');
            $query->where('a.marketplace_extension_id='.$db->quote($extension_id));
            $db->setQuery($query);
            $this->item = $db->loadObject();

            if (!empty($this->item)) {
                $this->item->images = array_filter(json_decode($this->item->images, true));
                $this->item->tags = json_decode($this->item->tags, true);
            }
        }

        return $this->item;
    }
}