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
 * View class for install extension.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @since       3.1
 */
class MarketplaceViewInstall extends JViewLegacy
{
    public function display($tpl = null)
    {
        $app = JFactory::getApplication();
        $this->item = $this->get('item');
        $this->download_url = $app->input->getBase64('download_link');

        if (empty($this->download_url)) {
            $app->enqueueMessage(JText::_('COM_MARKETPLACE_'.$this->getName().'_PLEASE_REQUEST_AGAIN'));
        }

        $ajaxUrl = JUri::base().'components/com_marketplace/restore.php';

        $updateScript = <<<ENDSCRIPT
var joomlaupdate_filepath = '';
var joomlaupdate_password = '';
var joomlaupdate_totalsize = 0;
var joomlaupdate_ajax_url = '$ajaxUrl';
ENDSCRIPT;

        $document = JFactory::getDocument();
        $document->addScript('../media/com_joomlaupdate/json2.js');
        $document->addScript('../media/com_joomlaupdate/encryption.js');
        $document->addScriptDeclaration($updateScript);

        parent::display($tpl);
    }
}