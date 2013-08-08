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
 * Extensions Marketplace Model
 *
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @since       3.1
 */
class MarketplaceModelExtensions extends JModelList
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

			);
		}

		parent::__construct($config);
	}

	public function getTotalExtensions()
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__marketplace_extensions');
		$db->setQuery($query);
		return $db->loadResult();
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
		$query->select('a.marketplace_extension_id, a.icon, a.name, a.tags, a.plan,a.pathway, a.author, a.purchased, a.purchased_date, a.version, a.reviews, a.rating, a.item_url, a.author_url, a.details_url');

		$query->from($db->quoteName('#__marketplace_extensions').' AS a');
		
		// Join store repository
		$query->select('s.name AS marketplace_repository_name');
		$query->join('LEFT', $db->quoteName('#__marketplace_repositories').' AS s ON s.marketplace_repository_id = a.marketplace_repository_id');
		
		// Join installed extensions
		$query->select('e.extension_id');
		$query->join('LEFT', $db->quoteName('#__extensions').' AS e ON e.element = a.identifier');

        // Join updates extensions
        $query->select('u.update_id');
        $query->join('LEFT', $db->quoteName('#__updates').' AS u ON (u.element = a.identifier AND e.extension_id = u.extension_id)');
		
		$query->where('a.name!=""');
		
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			$search = $db->Quote('%' . $db->escape($search, true) . '%');
			$query->where('(a.name LIKE ' . $search . ')');
		}
		
		$marketplace_repository_id = $this->getState('filter.marketplace_repository_id');
		if ($marketplace_repository_id>0) {
			$query->where('a.marketplace_repository_id='.$db->quote($marketplace_repository_id));
		}

        $section = $this->getState('filter.section');
		if (!empty($section)) {
			$query->where('a.section='.$db->quote($section));
		}
		
		$type = $this->getState('filter.tags');
		if (!empty($type)) {
            $query->where('a.tags LIKE '. $this->_db->quote('%' . $this->_db->escape($type, true) . '%'));
		}

        $purchased = $this->getState('filter.purchased');
        if (is_int($purchased)) {
            $query->where('a.purchased='.$db->quote($purchased));
        }
		
		$category = $this->getState('filter.category');
		if (!empty($category)) {
			$query->where('a.pathway LIKE '. $this->_db->quote('%' . $this->_db->escape($category, true) . '%'));
		}
		
		$author = $this->getState('filter.author');
		if (!empty($author)) {
			$query->where('a.author='.$db->quote($author));
		}
		
		$plan = $this->getState('filter.plan');
		if (!empty($plan)) {
			$query->where('a.plan='.$db->quote($plan));
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
		$id	.= ':' . $this->getState('filter.marketplace_repository_id');
		$id	.= ':' . $this->getState('filter.section');
		$id	.= ':' . $this->getState('filter.tags');
		$id	.= ':' . $this->getState('filter.category');
		$id	.= ':' . $this->getState('filter.author');
		$id	.= ':' . $this->getState('filter.plan');
        $id	.= ':' . $this->getState('filter.purchased');
		
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
		
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$repository_id = $app->getUserStateFromRequest($this->context.'.filter.marketplace_repository_id', 'filter_marketplace_repository_id');
		$this->setState('filter.marketplace_repository_id', $repository_id);
		
		$section = $app->getUserStateFromRequest($this->context.'.filter.section', 'filter_section');
		$this->setState('filter.section', $section);
		
		$type = $app->getUserStateFromRequest($this->context.'.filter.tags', 'filter_tag');
		$this->setState('filter.tags', $type);

		$category = $app->getUserStateFromRequest($this->context.'.filter.category', 'filter_category');
		$this->setState('filter.category', $category);

		$author = $app->getUserStateFromRequest($this->context.'.filter.author', 'filter_author');
		$this->setState('filter.author', $author);
		
		$plan = $app->getUserStateFromRequest($this->context.'.filter.plan', 'filter_plan');
		$this->setState('filter.plan', $plan);

        $purchased = $app->getUserStateFromRequest($this->context.'.filter.purchased', 'filter_purchased');
        $this->setState('filter.purchased', $purchased);

		$this->setState('extension_message', $app->getUserState($this->context.'.extension_message'));
		
		$display = $app->getUserStateFromRequest($this->context.'.display', 'display');
		
		$app->setUserState('global.list.limit', 16 );

		parent::populateState($ordering, $direction);
	}
}
