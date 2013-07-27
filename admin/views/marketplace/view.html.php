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
 * View class for a list of extensions.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @since       3.1
 */
class MarketplaceViewMarketplace extends JViewLegacy
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
		$this->setModel(JModelLegacy::getInstance('extensions','marketplacemodel'), true);
		$this->state		 = $this->get('State');
		$this->items		 = $this->get('Items');
		$this->nr_extensions = $this->get('TotalExtensions');
		$this->pagination	 = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->addToolbar();
		// Render side bar
		$this->sidebar = JHtmlSidebar::render();
		
		$this->loadHelper('rating');
		$this->loadHelper('button');
		
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 */
	protected function addToolbar()
	{
		$canDo	= MarketplaceHelper::getActions();
		$type = $this->state->get('filter.type');
		$browse = $this->state->get('filter.browse');
		$suffix = empty($browse) ? 'EXTENSIONS' : strtoupper($browse);
		$this->browse = JText::_('COM_MARKETPLACE_COLLECTION_'.$suffix);
		
		JToolBarHelper::title(JText::sprintf('COM_MARKETPLACE_HEADER_' . $this->getName(),$this->browse), 'marketplace');
		
		$repository_id = $this->state->get('filter.store_repository_id');
		$countStores = MarketplaceHelper::getExtensionTotalStores();
		$stores = MarketplaceHelper::getExtensionStores();
		
		if ($canDo->get('marketplace.findextensions'))
		{
			if ($countStores) JToolBarHelper::custom($this->getName().'.findextensions', 'refresh', 'refresh', 'COM_MARKETPLACE_TOOLBAR_FIND_EXTENSIONS', false, false);
			JToolBarHelper::divider();
		}
		
		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_marketplace');
			JToolbarHelper::divider();
		}
		
		if (!empty($this->nr_extensions))
		{
			JHtmlSidebar::addFilter(
				JText::_('COM_MARKETPLACE_VALUE_REPOSITORY_SELECT'),
				'filter_store_repository_id',
				JHtml::_('select.options', $stores, 'value', 'text', $repository_id, true)
			);
			
			if ($this->state->get('filter.browse') == '') {
				JHtmlSidebar::addFilter(
					JText::_('COM_MARKETPLACE_VALUE_TYPE_SELECT'),
					'filter_type',
					JHtml::_('select.options', MarketplaceHelper::getExtensionTypes(), 'value', 'text', $this->state->get('filter.type'), true)
				);
			}
			
			JHtmlSidebar::addFilter(
				JText::_('COM_MARKETPLACE_VALUE_CATEGORY_SELECT'),
				'filter_category',
				JHtml::_('select.options', MarketplaceHelper::getExtensionCategories(), 'value', 'text', $this->state->get('filter.category'), true)
			);
			
			JHtmlSidebar::addFilter(
				JText::_('COM_MARKETPLACE_VALUE_AUTHOR_SELECT'),
				'filter_author',
				JHtml::_('select.options', MarketplaceHelper::getExtensionAuthors(), 'value', 'text', $this->state->get('filter.author'), true)
			);
			
			JHtmlSidebar::addFilter(
				JText::_('COM_MARKETPLACE_VALUE_PLAN_SELECT'),
				'filter_plan',
				JHtml::_('select.options', MarketplaceHelper::getExtensionPlans(), 'value', 'text', $this->state->get('filter.plan'), true)
			);
		}
		
		if ($countStores == 0) {
			JFactory::getApplication()->redirect('index.php?option=com_marketplace&view=repositories');
		}
		
		MarketplaceHelper::addSubmenu($this->getName());
	}
}