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
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @since       3.1
 */
abstract class MarketplaceHtmlManage
{
	/**
	 * Returns a published state on a grid
	 *
	 * @param   integer       $value			The state value.
	 * @param   integer       $i				The row index
	 * @param   boolean       $enabled			An optional setting for access control on the action.
	 * @param   string        $checkbox			An optional prefix for checkboxes.
	 *
	 * @return  string        The Html code
	 *
	 * @see JHtmlJGrid::state
	 *
	 * @since   2.5
	 */
	public static function state($task, $view,$value, $i, $enabled = true, $checkbox = 'cb')
	{
		$view = strtoupper($view);
		$states	= array(
			2	=> array(
				'',
				'COM_MARKETPLACE_'.$view.'_PROTECTED',
				'',
				'COM_MARKETPLACE_'.$view.'_PROTECTED',
				false,
				'protected',
				'protected'
			),
			1	=> array(
				'unpublish',
				'COM_MARKETPLACE_'.$view.'_ENABLED',
				'COM_MARKETPLACE_'.$view.'_DISABLE',
				'COM_MARKETPLACE_'.$view.'_ENABLED',
				false,
				'publish',
				'publish'
			),
			0	=> array(
				'publish',
				'COM_MARKETPLACE_'.$view.'_DISABLED',
				'COM_MARKETPLACE_'.$view.'_ENABLE',
				'COM_MARKETPLACE_'.$view.'_DISABLED',
				false,
				'unpublish',
				'unpublish'
			),
		);

		return JHtml::_('jgrid.state', $states, $value, $i, $task.'.', $enabled, true, $checkbox);
	}
}
