<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_marketplace
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_marketplace
 */
class MarketplaceControllerRepository extends JControllerForm
{
    protected $view_item = 'repositories';
    protected $view_list = 'repositories';

    /**
     * Find extensions.
     *
     * @return  void
     *
     * @since   3.1
     */
    public function find()
    {
        $input = JFactory::getApplication()->input;
        $marketplace_repository_id = $input->getInt('marketplace_repository_id');
        $timestamp = $input->getString('timestamp');
        $json = $input->getString('json');
        $items = json_decode($json, true);

        if (empty($items)) {
            $response = array('result' => true);
        } else {
            // Find updates
            $model	= $this->getModel('repository');
            $response = $model->importExtensions($marketplace_repository_id,$timestamp,$items);
        }

        echo json_encode($response);
        JFactory::getApplication()->close();
    }
}