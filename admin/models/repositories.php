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
 * Repositories Store Model
 *
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @since       3.1
 */
class MarketplaceModelRepositories extends JModelList
{

	/**
	 * Constructor override, defines a white list of column filters.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JModelList
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'a.store_repository_id',
				'a.name', 'a.location', 'a.type', 'a.published', 'a.last_check_timestamp'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to get the available languages database query.
	 *
	 * @return	JDatabaseQuery	The database query
	 */
	protected function _getListQuery()
	{
		$db     = JFactory::getDBO();
		$query  = $db->getQuery(true);

		// Select the required fields from the updates table
		$query->select('a.store_repository_id, a.name, a.location, a.type, a.published, a.last_check_timestamp');

		$query->from($db->quoteName('#__marketplace_repositories').' AS a');

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			$search = $db->Quote('%' . $db->escape($search, true) . '%');
			$query->where('(a.name LIKE ' . $search . ')');
		}

		// Add the list ordering clause.
		$listOrder	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		$query->order($db->escape($listOrder) . ' ' . $db->escape($orderDirn));

		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':' . $this->getState('filter.search');

		return parent::getStoreId($id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   null  $ordering   list order
	 * @param   null  $direction  direction in the list
	 *
	 * @return  void
	 */
	protected function populateState($ordering = 'a.name', $direction = 'asc')
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		$value = $app->getUserStateFromRequest($this->context .'.filter.search', 'filter_search');
		$this->setState('filter.search', $value);

		$this->setState('extension_message', $app->getUserState('com_marketplace.extension_message'));

		parent::populateState($ordering, $direction);
	}
}
