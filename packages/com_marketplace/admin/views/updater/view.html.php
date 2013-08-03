<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       2.5.7
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Updater store view
 *
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @since       3.1
 */
class MarketplaceViewUpdater extends JViewLegacy
{
	/**
	 * Display the view
	 *
	 * @param   null  $tpl  template to display
	 *
	 * @return mixed|void
	 */
	public function display($tpl=null)
	{
		$canDo	= MarketplaceHelper::getActions();
		
		if (!$canDo->get('marketplace.findextensions')) {
			JFactory::getApplication()->redirect('index.php?option=com_marketplace',JText::_('COM_MARKETPLACE_NO_AUTH'));
		}
		
		$return = JFactory::getApplication()->input->getString('return','index.php?option=com_marketplace&view=marketplace');
		
		$model = JModelLegacy::getInstance('updater','marketplacemodel');
		$this->repositories = $model->getRepositories();
		
		if (empty($this->repositories)) {
			JFactory::getApplication()->redirect('index.php?option=com_marketplace',JText::_('COM_MARKETPLACE_MSG_REPOSITORIES_NEED_PUBLISHED'),'error');
		}
		
		$json = json_encode($this->repositories);

        $version = new JVersion;
        $rel_version = $version->RELEASE;

		$updateScript = <<<ENDSCRIPT
var updater_return_url = '$return';
var repositories = $json;
var rel_version = '$rel_version';
ENDSCRIPT;

		// Load our Javascript
		$document = JFactory::getDocument();
		$document->addScript('../media/com_joomlaupdate/json2.js');
		$document->addScript('../media/com_marketplace/js/update.js');
		JHtml::_('script', 'system/progressbar.js', true, true);
		JHtml::_('stylesheet', 'media/mediamanager.css', array(), true);
		$document->addScriptDeclaration($updateScript);
		
		$this->addToolbar();
		
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JToolbarHelper::title(JText::_('COM_MARKETPLACE_HEADER_' . $this->getName()), 'marketplace.png');

		// Document
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_MARKETPLACE_TITLE_' . $this->getName()));

		// Render side bar
		$this->sidebar = JHtmlSidebar::render();
	}
}