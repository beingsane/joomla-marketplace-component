<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.marketplace
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Marketplace System Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  System.marketplace
 * @since       3.1
 */
class PlgSystemMarketplace extends JPlugin
{
    /**
     * Add support for select installer tab and fill install url field
     *
     * @return  void
     *
     * @since   3.1
     */
    public function onAfterDispatch()
    {
        $app    = JFactory::getApplication();
        $input  = $app->input;
        $doc    = JFactory::getDocument();
        $option = $input->getCmd('option');
        $view   = $input->getCmd('view');
        $install_url = $input->getBase64('install_url');
        if (!empty($install_url)) {
            $install_url = base64_decode($install_url);
        }

        if ($app->isAdmin() && $option == 'com_installer' && !empty($install_url) && (empty($view) || $view == 'installer'))
        {
            $doc->addScriptDeclaration("jQuery(document).ready(function() {jQuery('a[href=#url]').tab('show'); jQuery('#install_url').val('$install_url');}_;");
        }
    }
}