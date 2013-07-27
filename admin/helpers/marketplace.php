<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Marketplace component helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @since       3.1
 */
class MarketplaceHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string	$vName	The name of the active view.
	 *
	 * @return  void
	 * @since   3.1
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_MARKETPLACE_SUBMENU_MARKETPLACE'),
			'index.php?option=com_marketplace&view=marketplace',
			$vName == 'marketplace'
		);
		if (JFactory::getUser()->authorise('repositories.manage','com_marketplace')) {
			JHtmlSidebar::addEntry(
				JText::_('COM_MARKETPLACE_SUBMENU_REPOSITORIES'),
				'index.php?option=com_marketplace&view=repositories',
				$vName == 'repositories');
		}
		if (JFactory::getUser()->authorise('core.manage','com_installer')) {
			JHtmlSidebar::addEntry(
				JText::_('COM_MARKETPLACE_SUBMENU_UPDATES'),
				'index.php?option=com_installer&view=update',
				$vName == 'update'
			);
		}
	}

	/**
	 * Get a list of extension plans
	 * 
	 * @return  array  An array of stdClass objects.
	 * 
	 * @since 3.1
	 */
	public static function getExtensionPlans()
	{
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('DISTINCT e.plan');
		$query->from('#__marketplace_extensions AS e');
		$query->where('e.plan!=""');
		$query->order('e.plan');
		$db->setQuery($query);
		$extensions = $db->loadObjectList();
		foreach ($extensions as $extension) {
			$options[] = JHtml::_('select.option', $extension->plan, $extension->plan);
		}
		
		return $options;
	}

	/**
	 * Total repositories
	 * 
	 * @since	3.1
	 */
	public static function getExtensionTotalStores()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('COUNT(s.store_repository_id)');
		$query->from('#__marketplace_repositories AS s');
		$query->where('s.published>=1');
		$db->setQuery($query);
		
		return $db->loadResult();
	}

	/**
	 * Get a list of update sites
	 * 
	 * @return  array  An array of stdClass objects.
	 * 
	 * @since 3.1
	 */
	public static function getExtensionStores($state=1)
	{
		$input = JFactory::getApplication()->input;
		
		$type = $input->getCmd('filter_browse','');
		
		$db = JFactory::getDBO();
		
		$collections = array_slice(array_values(MarketplaceHelperButton::$collections),1);
		$in = '"'.implode('","',$collections).'"';
		if (in_array($type,$collections)) {
			$wheres = 'e.type IN ('.$db->quote($type).')';
		} else {
			$wheres = 'e.type NOT IN ('.$in.')';
		}
		
		$query = $db->getQuery(true);
		$query->select('s.name, s.store_repository_id');
		$query->from('#__marketplace_repositories AS s');
		$query->join('LEFT','#__marketplace_extensions AS e ON (e.store_repository_id = s.store_repository_id)');
		$query->where('s.published>='.$db->quote($state));
		$query->where($wheres);
		$query->group('e.store_repository_id');
		$query->order('s.name ASC');
		$db->setQuery($query);
		$repositories = $db->loadObjectList();

		$options = array();
		foreach ($repositories as $repository)
		{
			$options[] = JHtml::_('select.option', $repository->store_repository_id, $repository->name);
		}

		return $options;
	}

	/**
	 * Get a list of update types
	 * 
	 * @return  array  An array of stdClass objects.
	 * 
	 * @since 3.1
	 */
	public static function getExtensionTypes()
	{
		$collections = '"'.implode('","',array_slice(array_values(MarketplaceHelperButton::$collections),1)).'"';
		$wheres = 'e.type NOT IN ('.$collections.')';
		
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('DISTINCT e.type');
		$query->from('#__marketplace_extensions AS e');
		$query->where($wheres);
		$query->order('e.type');
		$db->setQuery($query);
		$extensions = $db->loadObjectList();
		foreach ($extensions as $extension) {
			$options[] = JHtml::_('select.option', $extension->type, $extension->type);
		}
		
		return $options;
	}

	/**
	 * Get a list of extension authors
	 * 
	 * @return  array  An array of stdClass objects.
	 * 
	 * @since 3.1
	 */
	public static function getExtensionAuthors()
	{
		$input = JFactory::getApplication()->input;
		
		$type = $input->getCmd('filter_browse');
		
		$db = JFactory::getDBO();
		
		$collections = array_slice(array_values(MarketplaceHelperButton::$collections),1);
		$in = '"'.implode('","',$collections).'"';
		if (in_array($type,$collections)) {
			$wheres = 'e.type IN ('.$db->quote($type).')';
		} else {
			$wheres = 'e.type NOT IN ('.$in.')';
		}
		
		$query = $db->getQuery(true);
		$query->select('s.name, s.store_repository_id');
		$query->from('#__marketplace_repositories AS s');
		$query->join('LEFT','#__marketplace_extensions AS e ON (e.store_repository_id = s.store_repository_id)');
		$query->where($wheres);
		$query->group('e.store_repository_id');
		$query->order('s.name ASC');
		$db->setQuery($query);
		$repositories = $db->loadObjectList();

		$options = array();
		foreach ($repositories as $repository)
		{
			$options[] = JHtml::_('select.option', '<OPTGROUP>', $repository->name);
			
			$query 	= $db->getQuery(true);
			$query->select('DISTINCT e.author');
			$query->from('#__marketplace_extensions AS e');
			$query->where($wheres);
			$query->where('e.store_repository_id='.$db->quote($repository->store_repository_id));
			$query->order('e.author');
			$db->setQuery($query);
			$extensions = $db->loadObjectList();
			foreach ($extensions as $extension) {
				$options[] = JHtml::_('select.option', $extension->author, $extension->author);
			}
			
			$options[] = JHtml::_('select.option', '</OPTGROUP>', '');
		}

		return $options;
	}

	/**
	 * Get a list of extension categories
	 * 
	 * @return  array  An array of stdClass objects.
	 * 
	 * @since 3.1
	 */
	public static function getExtensionCategories()
	{
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$input = JFactory::getApplication()->input;
		
		$type = $input->getCmd('filter_browse');
		
		$collections = array_slice(array_values(MarketplaceHelperButton::$collections),1);
		$in = '"'.implode('","',$collections).'"';
		if (in_array($type,$collections)) {
			$wheres = 'e.type IN ('.$db->quote($type).')';
		} else {
			$wheres = 'e.type NOT IN ('.$in.')';
		}
		
		
		$query->select('DISTINCT e.category');
		$query->from('#__marketplace_extensions AS e');
		$query->where($wheres);
		$query->order('e.category');
		$db->setQuery($query);
		$extensions = $db->loadObjectList();
		foreach ($extensions as $extension) {
			$options[] = JHtml::_('select.option', $extension->category, $extension->category);
		}
		
		return $options;
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   integer  The category ID.
	 * @param   integer  The article ID.
	 *
	 * @return  JObject
	 * @since   3.1
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.delete', 'marketplace.findextensions', 'repositories.manage', 'repositories.manage.state'
		);

		foreach ($actions as $action)
		{
			$result->set($action,	$user->authorise($action, 'com_marketplace'));
		}

		return $result;
	}
}