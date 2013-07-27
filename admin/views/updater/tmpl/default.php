<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<p class="nowarning"><?php echo JText::_('COM_MARKETPLACE_VIEW_UPDATER_INPROGRESS') ?></p>
<div class="joomlaupdate_spinner" ></div>
<div id="update-progress">
	<div id="extprogress">
		<div class="extprogrow">
			<?php
			echo JHtml::_(
				'image', 'media/bar.gif', JText::_('COM_MARKETPLACE_VIEW_UPDATER_PROGRESS'),
				array('class' => 'progress', 'id' => 'progress'), true
			); ?>
		</div>
		<div class="extprogrow">
			<span class="extlabel"><?php echo JText::_('COM_MARKETPLACE_VIEW_UPDATER_NAME'); ?></span>
			<span class="extvalue" id="repo_name"></span>
		</div>
		<div class="extprogrow">
			<span class="extlabel"><?php echo JText::_('COM_MARKETPLACE_VIEW_UPDATER_URL'); ?></span>
			<span class="extvalue" id="repo_url"></span>
		</div>
	</div>
</div>
