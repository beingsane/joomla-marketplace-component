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
        $user = JFactory::getUser();

		JHtmlSidebar::addEntry(
			JText::_('COM_MARKETPLACE_SUBMENU_MARKETPLACE'),
			'index.php?option=com_marketplace&view=marketplace',
			$vName == 'marketplace'
		);
		if ($user->authorise('repositories.manage','com_marketplace')) {
			JHtmlSidebar::addEntry(
				JText::_('COM_MARKETPLACE_SUBMENU_REPOSITORIES'),
				'index.php?option=com_marketplace&view=repositories',
				$vName == 'repositories');
		}
		if ($user->authorise('core.manage','com_installer')) {
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
		$repository_id = $input->getInt('filter_marketplace_repository_id');
		$type = $input->getString('filter_tag','');
		$category = $input->getString('filter_category','');
		$author = $input->getString('filter_author','');
		
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('DISTINCT e.section');
		$query->from('#__marketplace_extensions AS e');
		if ($repository_id>0) {
			$query->where('e.marketplace_repository_id='.$db->quote($repository_id));
		}
		if (!empty($type)) {
            $query->where('e.tags LIKE '. $db->quote('%' . $db->escape($type, true) . '%'));
		}
		if (!empty($category)) {
            $query->where('e.pathway LIKE '. $db->quote('%' . $db->escape($category, true) . '%'));
		}
		if (!empty($author)) {
			$query->where('e.author='.$db->quote($author));
		}
		$query->order('e.section');
		$db->setQuery($query);
		$extensions = $db->loadObjectList();
		foreach ($extensions as $extension) {
			$options[] = JHtml::_('select.option', $extension->section, $extension->section);
		}
		
		return $options;
	}
	
	/**
	 * Get a list of tmpl plans
	 * 
	 * @return  array  An array of stdClass objects.
	 * 
	 * @since 3.1
	 */
	public static function getExtensionPlans()
	{
		$input = JFactory::getApplication()->input;
		$repository_id = $input->getInt('filter_marketplace_repository_id');
        $section = $input->getString('filter_section','');
		$type = $input->getString('filter_tag','');
		$category = $input->getString('filter_category','');
		$author = $input->getString('filter_author','');
		
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('DISTINCT e.plan');
		$query->from('#__marketplace_extensions AS e');
		
		if ($repository_id>0) {
			$query->where('e.marketplace_repository_id='.$db->quote($repository_id));
		}
		if (!empty($section)) {
			$query->where('e.section='.$db->quote($section));
		}
		if (!empty($type)) {
            $query->where('e.tags LIKE '. $db->quote('%' . $db->escape($type, true) . '%'));
		}
		if (!empty($category)) {
            $query->where('e.pathway LIKE '. $db->quote('%' . $db->escape($category, true) . '%'));
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
		
		$query->select('COUNT(s.marketplace_repository_id)');
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
		$section = $input->getString('filter_section','');
		$type = $input->getString('filter_tag','');
		$category = $input->getString('filter_category','');
		$author = $input->getString('filter_author','');
		
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		$query->select('s.name, s.marketplace_repository_id');
		$query->from('#__marketplace_repositories AS s');
		$query->join('LEFT','#__marketplace_extensions AS e ON (e.marketplace_repository_id = s.marketplace_repository_id)');
		$query->where('s.published>='.$db->quote($state));
		if (!empty($section)) {
			$query->where('e.section='.$db->quote($section));
		}
		if (!empty($type)) {
            $query->where('e.tags LIKE '. $db->quote('%' . $db->escape($type, true) . '%'));
		}
		if (!empty($category)) {
            $query->where('e.pathway LIKE '. $db->quote('%' . $db->escape($category, true) . '%'));
		}
		if (!empty($author)) {
			$query->where('e.author='.$db->quote($author));
		}
		$query->group('e.marketplace_repository_id');
		$query->order('s.name ASC');
		$db->setQuery($query);
		$repositories = $db->loadObjectList();

		$options = array();
		foreach ($repositories as $repository)
		{
			$options[] = JHtml::_('select.option', $repository->marketplace_repository_id, $repository->name);
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
		$repository_id = $input->getInt('filter_marketplace_repository_id');
		$section = $input->getString('filter_section','');
		$category = $input->getString('filter_category','');
		$author = $input->getString('filter_author','');
		
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('DISTINCT e.tags');
		$query->from('#__marketplace_extensions AS e');
		if ($repository_id>0) {
			$query->where('e.marketplace_repository_id='.$db->quote($repository_id));
		}
		if (!empty($section)) {
			$query->where('e.section='.$db->quote($section));
		}
		if (!empty($category)) {
            $query->where('e.pathway LIKE '. $db->quote('%' . $db->escape($category, true) . '%'));
		}
		if (!empty($author)) {
			$query->where('e.author='.$db->quote($author));
		}
		$query->order('e.tags');
		$db->setQuery($query);
		$extensions = $db->loadObjectList();
		
		$options = array();
        $types = array();
		foreach ($extensions as $extension) {
            $types = array_merge($types, json_decode($extension->tags, true));
		}
        $types = array_unique($types);
        $types = array_filter($types);
        foreach ($types as $type) {
            $options[] = JHtml::_('select.option', $type, $type);
        }
		
		return $options;
	}

	/**
	 * Get a list of tmpl authors
	 * 
	 * @return  array  An array of stdClass objects.
	 * 
	 * @since 3.1
	 */
	public static function getExtensionAuthors()
	{
		$input = JFactory::getApplication()->input;
		$repository_id = $input->getInt('filter_marketplace_repository_id');
		$section = $input->getString('filter_section','');
		$type = $input->getString('filter_tag','');
		$category = $input->getString('filter_category','');
		
		$db 	= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('s.name, s.marketplace_repository_id');
		$query->from('#__marketplace_repositories AS s');
		$query->join('LEFT','#__marketplace_extensions AS e ON (e.marketplace_repository_id = s.marketplace_repository_id)');
		if ($repository_id>0) {
			$query->where('e.marketplace_repository_id='.$db->quote($repository_id));
		}
		if (!empty($section)) {
			$query->where('e.section='.$db->quote($section));
		}
		if (!empty($type)) {
            $query->where('e.tags LIKE '. $db->quote('%' . $db->escape($type, true) . '%'));
		}
		$query->group('e.marketplace_repository_id');
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
				$query->where('e.marketplace_repository_id='.$db->quote($repository_id));
			}
			if (!empty($collection)) {
				$query->where('e.collection='.$db->quote($collection));
			}
			if (!empty($type)) {
                $query->where('e.tags LIKE '. $db->quote('%' . $db->escape($type, true) . '%'));
			}
			if (!empty($category)) {
                $query->where('e.pathway LIKE '. $db->quote('%' . $db->escape($category, true) . '%'));
			}
			$query->where('e.marketplace_repository_id='.$db->quote($repository->marketplace_repository_id));
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
	 * Get a list of tmpl categories
	 * 
	 * @return  array  An array of stdClass objects.
	 * 
	 * @since 3.1
	 */
	public static function getExtensionCategories()
	{	
		$input = JFactory::getApplication()->input;
		$repository_id = $input->getInt('filter_marketplace_repository_id');
		$section = $input->getString('filter_section','');
		$type = $input->getString('filter_tag','');
		$author = $input->getString('filter_author','');
		
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('DISTINCT e.pathway');
		$query->from('#__marketplace_extensions AS e');
		if ($repository_id>0) {
			$query->where('e.marketplace_repository_id='.$db->quote($repository_id));
		}
		if (!empty($section)) {
			$query->where('e.section='.$db->quote($section));
		}
		if (!empty($type)) {
            $query->where('e.tags LIKE '. $db->quote('%' . $db->escape($type, true) . '%'));
		}
		if (!empty($author)) {
			$query->where('e.author='.$db->quote($author));
		}
		$query->order('e.pathway');
		$db->setQuery($query);
		$extensions = $db->loadObjectList();
		$options = array();
        $categories = array();
		foreach ($extensions as $extension) {
            $categories = array_merge($categories, array($extension->pathway));
		}
        $categories = array_unique($categories);

        foreach ($categories as $category) {
            $options[] = JHtml::_('select.option', $category, $category);
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