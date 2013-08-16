<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Install Marketplace Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @since       3.1
 */
class MarketplaceControllerInstall extends JControllerLegacy
{
    public function fromdir()
    {
        $tmp_file = JFactory::getApplication()->input->getCmd('tmp_file');
        $model = $this->getModel('install','marketplacemodel');
        $response = $model->createRestorationFile($tmp_file);
    }

    public function prepareextraction()
    {
        $tmp_file = JFactory::getApplication()->input->getCmd('tmp_file');
        $model = $this->getModel('install','marketplacemodel');
        $response = $model->createRestorationFile($tmp_file);

        echo json_encode($response);
        JFactory::getApplication()->close();
    }

    public function download()
    {
        $model = $this->getModel('install','marketplacemodel');
        $response = $model->download();

        echo json_encode($response);
        JFactory::getApplication()->close();
    }
}