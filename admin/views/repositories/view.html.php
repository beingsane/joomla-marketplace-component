<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       3.1
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Repositories store view
 *
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @since       3.1
 */
class MarketplaceViewRepositories extends JViewLegacy
{
	/**
	 * @var object item list
	 */
	protected $items;

	/**
	 * @var object pagination information
	 */
	protected $pagination;

	/**
	 * @var object model state
	 */
	protected $state;

	/**
	 * Display the view
	 *
	 * @param   null  $tpl  template to display
	 *
	 * @return mixed|void
	 */
	public function display($tpl=null)
	{
		// Get data from the model
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->user			= JFactory::getUser();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		JFactory::getApplication()->enqueueMessage(JText::_('COM_MARKETPLACE_MSG_WARNINGS_REPOSITORIES_NOTICE'), 'warning');
		
		// Include the component HTML helpers.
		JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
		
		$this->addToolbar();
		// Render side bar
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 */
	protected function addToolbar()
	{
		$this->canDo	= MarketplaceHelper::getActions();
		
		if (!$this->canDo->get('repositories.manage')) {
			JFactory::getApplication()->redirect('index.php?option=com_marketplace',JText::_('COM_MARKETPLACE_NO_AUTH'));
		}
		
		JToolBarHelper::title(JText::_('COM_MARKETPLACE_HEADER_' . $this->getName()), 'marketplace.png');
		
		if ($this->canDo->get('repositories.manage.state') && !empty($this->items)) {
			JToolbarHelper::archiveList($this->getName().'.archive');
			JToolbarHelper::unpublishList($this->getName().'.unpublish');
			JToolbarHelper::publishList($this->getName().'.publish');
			JToolbarHelper::divider();
		}
		
		if ($this->canDo->get('core.delete') && !empty($this->items)) {
			JToolbarHelper::deleteList('', $this->getName().'.delete', 'JTOOLBAR_DELETE');
			JToolbarHelper::divider();
		}
		
		if ($this->canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_marketplace');
			JToolbarHelper::divider();
		}
		
		MarketplaceHelper::addSubmenu($this->getName());
	}
}