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
class MarketplaceViewPurchases extends JViewLegacy
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
        $model = $this->getModel();

        $this->state		 = $this->get('State');
        $model->setState('filter.section','');
        $model->setState('filter.purchased',1);
        $this->items		 = $this->get('Items');
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
        $this->loadHelper('button');
        $purchasedExtensions = MarketplaceHelper::getTotalPurchasedExtensions();
        $countStores = MarketplaceHelper::getExtensionTotalStores();

        if ($countStores == 0) {
            JFactory::getApplication()->redirect('index.php?option=com_marketplace&view=repositories', JText::_('COM_MARKETPLACE_MSG_REPOSITORIES_NO_REPOSITORIES'),'warning');
        }

        if ($purchasedExtensions == 0) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_MARKETPLACE_MSG_NO_PURCHASED_EXTENSIONS'),'warning');
        }

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


        JToolBarHelper::title(JText::_('COM_MARKETPLACE_HEADER_' . $this->getName()), 'marketplace');

        if ($canDo->get('core.admin'))
        {
            JToolbarHelper::preferences('com_marketplace');
            JToolbarHelper::divider();
        }

        MarketplaceHelper::addSubmenu($this->getName());
    }
}