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
				'a.store_extension_id','a.name','a.type','a.version','a.infourl','a.detailsurl','a.element','a.type','a.folder','a.image','a.description','a.author'
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
	 * Removes all of the updates from the table.
	 *
	 * @return  boolean result of operation
	 *
	 * @since   3.1
	 */
	public function purge()
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->delete('#__marketplace_extensions');
		$db->setQuery($query);
		if ($db->execute())
		{
			// Reset the last update check timestamp
			$query = $db->getQuery(true)
				->update($db->quoteName('#__marketplace_repositories'))
				->set($db->quoteName('last_check_timestamp') . ' = ' . $db->quote(0));
			$db->setQuery($query);
			$db->execute();
			$this->_message = JText::_('COM_MARKETPLACE_PURGED_EXTENSIONS');
			return true;
		}
		else
		{
			$this->_message = JText::_('COM_MARKETPLACE_FAILED_TO_PURGE_EXTENSIONS');
			return false;
		}
	}

	/**
	 * Method to find available extensions.
	 *
	 * @param   int  $cache_timeout  time before refreshing the cached updates
	 *
	 * @return  bool
	 * 
	 * @since	3.1
	 */
	public function findExtensions($cache_timeout = 0)
	{
		$updater = JMarketplace::getInstance();
		return $updater->findUpdates($cache_timeout);
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
		$query->select('a.store_extension_id, a.name, a.type, a.version, a.infourl, a.detailsurl, a.element, a.type, a.folder, a.description, a.author, a.image, a.plan, a.reviews, a.rating, a.category');

		$query->from($db->quoteName('#__marketplace_extensions').' AS a');
		
		// Join store repository
		$query->select('s.name AS store_repository_name');
		$query->join('LEFT', $db->quoteName('#__marketplace_repositories').' AS s ON s.store_repository_id = a.store_repository_id');
		
		// Join installed extensions
		$query->select('e.extension_id');
		$query->join('LEFT', $db->quoteName('#__extensions').' AS e ON e.element = a.element');
		
		$query->where('a.name!=""');
		
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			$search = $db->Quote('%' . $db->escape($search, true) . '%');
			$query->where('(a.name LIKE ' . $search . ')');
		}
		
		$store_repository_id = $this->getState('filter.store_repository_id');
		if ($store_repository_id>0) {
			$query->where('a.store_repository_id='.$db->quote($store_repository_id));
		}
		$author = $this->getState('filter.author');
		if (!empty($author)) {
			$query->where('a.author='.$db->quote($author));
		}
		$category = $this->getState('filter.category');
		if (!empty($category)) {
			$query->where('a.category='.$db->quote($category));
		}
		$type = $this->getState('filter.type');
		if (!empty($type)) {
			$query->where('a.type='.$db->quote($type));
		}
		
		$browse = $this->getState('filter.browse');
		if ($browse == 'template') {
			$query->where('a.type="template"');
		} else {
			$query->where('a.type!="template"');
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
		$id	.= ':' . $this->getState('filter.store_repository_id');
		$id	.= ':' . $this->getState('filter.author');
		$id	.= ':' . $this->getState('filter.category');
		$id	.= ':' . $this->getState('filter.type');
		$id	.= ':' . $this->getState('filter.plan');
		
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
		
		$repository_id = $app->getUserStateFromRequest($this->context.'.filter.store_repository_id', 'filter_store_repository_id');
		$this->setState('filter.store_repository_id', $repository_id);
		
		$author = $app->getUserStateFromRequest($this->context.'.filter.author', 'filter_author');
		$this->setState('filter.author', $author);
		
		$category = $app->getUserStateFromRequest($this->context.'.filter.category', 'filter_category');
		$this->setState('filter.category', $category);
		
		$browse = $app->getUserStateFromRequest($this->context.'.filter.browse', 'filter_browse');
		$this->setState('filter.browse', $browse);
		
		$type = $app->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', $browse);
		$this->setState('filter.type', $type);
		
		$plan = $app->getUserStateFromRequest($this->context.'.filter.plan', 'filter_plan');
		$this->setState('filter.plan', $plan);

		$this->setState('extension_message', $app->getUserState($this->context.'.extension_message'));
		
		$app->setUserState('global.list.limit', ($browse == 'template') ? 18 : 16 );

		parent::populateState($ordering, $direction);
	}
}
