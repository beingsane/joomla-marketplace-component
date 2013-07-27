<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_marketplace
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_marketplace
 */
class MarketplaceControllerRepositories extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  3.1
	 */
	protected $text_prefix = 'COM_MARKETPLACE_REPOSITORIES';
	
	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 *
	 * @return	JModel
	 * @since	3.1
	 */
	public function getModel($name = 'Repository', $prefix = 'MarketplaceModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}
