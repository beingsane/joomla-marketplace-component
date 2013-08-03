<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

if (!JFactory::getUser()->authorise('core.manage', 'com_marketplace'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

JLoader::register('MarketplaceHelperButton', __DIR__ . '/helpers/button.php');
JLoader::register('MarketplaceHelper', __DIR__ . '/helpers/marketplace.php');

$controller = JControllerLegacy::getInstance('Marketplace');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
