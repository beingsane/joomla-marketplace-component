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
class MarketplaceViewExtension extends JViewLegacy
{
    /**
     * @var object item list
     */
    protected $item;

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
        $this->setModel(JModelLegacy::getInstance('extension','marketplacemodel'), true);
        $this->item	= $this->get('Item');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $this->addToolbar();

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
        JToolBarHelper::title(JText::_('COM_MARKETPLACE_HEADER_' . $this->getName()), 'marketplace');

        JToolbarHelper::back();

        MarketplaceHelper::addSubmenu($this->getName());
    }
}