<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

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
        }

        return $this->item;
    }
}