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
	 * Get a list of collections
	 * 
	 * @return  array  An array of stdClass objects.
	 * 
	 * @since 3.1
	 */
	public static function getCollections()
	{
		$input = JFactory::getApplication()->input;
		$repository_id = $input->getInt('filter_store_repository_id');
		$type = $input->getCmd('filter_type','');
		$category = $input->getCmd('filter_category','');
		$author = $input->getCmd('filter_author','');
		
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('DISTINCT e.collection');
		$query->from('#__marketplace_extensions AS e');
		if ($repository_id>0) {
			$query->where('e.store_repository_id='.$db->quote($repository_id));
		}
		if (!empty($type)) {
			$query->where('e.type='.$db->quote($type));
		}
		if (!empty($category)) {
			$query->where('e.category='.$db->quote($category));
		}
		if (!empty($author)) {
			$query->where('e.author='.$db->quote($author));
		}
		$query->order('e.collection');
		$db->setQuery($query);
		$extensions = $db->loadObjectList();
		foreach ($extensions as $extension) {
			$options[] = JHtml::_('select.option', $extension->collection, $extension->collection);
		}
		
		return $options;
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
		$input = JFactory::getApplication()->input;
		$repository_id = $input->getInt('filter_store_repository_id');
		$collection = $input->getCmd('filter_collection','');
		$type = $input->getCmd('filter_type','');
		$category = $input->getCmd('filter_category','');
		$author = $input->getCmd('filter_author','');
		
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('DISTINCT e.plan');
		$query->from('#__marketplace_extensions AS e');
		
		if ($repository_id>0) {
			$query->where('e.store_repository_id='.$db->quote($repository_id));
		}
		if (!empty($collection)) {
			$query->where('e.collection='.$db->quote($collection));
		}
		if (!empty($type)) {
			$query->where('e.type='.$db->quote($type));
		}
		if (!empty($category)) {
			$query->where('e.category='.$db->quote($category));
		}
		if (!empty($author)) {
			$query->where('e.author='.$db->quote($author));
		}
		$query->order('e.plan');
		$db->setQuery($query);
		$extensions = $db->loadObjectList();
		$options = array();
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
	 * Get a list of repositories
	 * 
	 * @return  array  An array of stdClass objects.
	 * 
	 * @since 3.1
	 */
	public static function getExtensionStores($state=1)
	{
		$input = JFactory::getApplication()->input;
		$collection = $input->getCmd('filter_collection','');
		$type = $input->getCmd('filter_type','');
		$category = $input->getCmd('filter_category','');
		$author = $input->getCmd('filter_author','');
		
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		$query->select('s.name, s.store_repository_id');
		$query->from('#__marketplace_repositories AS s');
		$query->join('LEFT','#__marketplace_extensions AS e ON (e.store_repository_id = s.store_repository_id)');
		$query->where('s.published>='.$db->quote($state));
		if (!empty($collection)) {
			$query->where('e.collection='.$db->quote($collection));
		}
		if (!empty($type)) {
			$query->where('e.type='.$db->quote($type));
		}
		if (!empty($category)) {
			$query->where('e.category='.$db->quote($category));
		}
		if (!empty($author)) {
			$query->where('e.author='.$db->quote($author));
		}
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
		$input = JFactory::getApplication()->input;
		$repository_id = $input->getInt('filter_store_repository_id');
		$collection = $input->getCmd('filter_collection','');
		$category = $input->getCmd('filter_category','');
		$author = $input->getCmd('filter_author','');
		
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('DISTINCT e.type');
		$query->from('#__marketplace_extensions AS e');
		if ($repository_id>0) {
			$query->where('e.store_repository_id='.$db->quote($repository_id));
		}
		if (!empty($collection)) {
			$query->where('e.collection='.$db->quote($collection));
		}
		if (!empty($category)) {
			$query->where('e.category='.$db->quote($category));
		}
		if (!empty($author)) {
			$query->where('e.author='.$db->quote($author));
		}
		$query->order('e.type');
		$db->setQuery($query);
		$extensions = $db->loadObjectList();
		
		$options = array();
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
		$repository_id = $input->getInt('filter_store_repository_id');
		$collection = $input->getCmd('filter_collection','');
		$type = $input->getCmd('filter_type','');
		$category = $input->getCmd('filter_category','');
		
		$db 	= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('s.name, s.store_repository_id');
		$query->from('#__marketplace_repositories AS s');
		$query->join('LEFT','#__marketplace_extensions AS e ON (e.store_repository_id = s.store_repository_id)');
		if ($repository_id>0) {
			$query->where('e.store_repository_id='.$db->quote($collection));
		}
		if (!empty($collection)) {
			$query->where('e.collection='.$db->quote($collection));
		}
		if (!empty($type)) {
			$query->where('e.type='.$db->quote($type));
		}
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
			if ($repository_id>0) {
				$query->where('e.store_repository_id='.$db->quote($repository_id));
			}
			if (!empty($collection)) {
				$query->where('e.collection='.$db->quote($collection));
			}
			if (!empty($type)) {
				$query->where('e.type='.$db->quote($type));
			}
			if (!empty($category)) {
				$query->where('e.category='.$db->quote($category));
			}
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
		$input = JFactory::getApplication()->input;
		$repository_id = $input->getInt('filter_store_repository_id');
		$collection = $input->getCmd('filter_collection','');
		$type = $input->getCmd('filter_type','');
		$author = $input->getCmd('filter_author','');
		
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('DISTINCT e.category');
		$query->from('#__marketplace_extensions AS e');
		if ($repository_id>0) {
			$query->where('e.store_repository_id='.$db->quote($repository_id));
		}
		if (!empty($collection)) {
			$query->where('e.collection='.$db->quote($collection));
		}
		if (!empty($type)) {
			$query->where('e.type='.$db->quote($type));
		}
		if (!empty($author)) {
			$query->where('e.author='.$db->quote($author));
		}
		$query->order('e.category');
		$db->setQuery($query);
		$extensions = $db->loadObjectList();
		$options = array();
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