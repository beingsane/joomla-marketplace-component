<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_marketplace
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

defined('_JEXEC') or die;

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
		// Get the caching duration
		$component = JComponentHelper::getComponent('com_marketplace');
		$params = $component->params;
		$cache_timeout = $params->get('cachetimeout', 6, 'int');
		$cache_timeout = 3600 * $cache_timeout;

		// Find updates
		$model	= $this->getModel('extensions');
		$result = $model->findExtensions($cache_timeout);
		
		echo $result;
		JFactory::getApplication()->close();
	}
}