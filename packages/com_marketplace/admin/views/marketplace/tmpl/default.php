<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('bootstrap.tooltip');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_(JFactory::getUri());?>" method="post" name="adminForm" id="adminForm" class="form-search">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
	<?php if ($this->nr_extensions == 0): ?>
	<div class="hero-unit">
		    <h3><?php echo JText::_('COM_MARKETPLACE'); ?></h3>
		    <p><?php echo JText::_('COM_MARKETPLACE_MARKETPLACE_INTRODUCTION'); ?></p>
		    <p>
		    <a onclick="Joomla.submitbutton('marketplace.findextensions')" class="btn btn-primary btn-large"><i class="icon-refresh"></i>&nbsp;&nbsp;<?php echo JText::_('COM_MARKETPLACE_TOOLBAR_FIND_EXTENSIONS'); ?></a>
		    </p>
		    </div>
	<?php else: ?>
		<?php echo $this->loadTemplate('filter'); ?>
		<?php if (count($this->items) || $this->escape($this->state->get('filter.search'))) : ?>
			<?php if (empty($this->items[0]->display) || $this->items[0]->display != 'template'): ?>
				<?php echo $this->loadTemplate('extensions'); ?>
			<?php else: ?>
				<?php echo $this->loadTemplate('templates'); ?>
			<?php endif; ?>
			<div class="row-fluid">
				<div class="span12 pagination pagination-centered">
					<?php echo $this->pagination->getListFooter(); ?>
				</div>
			</div>
		<?php else : ?>
			    
		<?php endif; ?>
	<?php endif; ?>
		<input type="hidden" name="display" id="display" value="<?php echo $this->items[0]->display; ?>">
        <input type="hidden" name="option" id="option" value="com_marketplace" />
		<input type="hidden" name="cid[]" id="cid" value="0" />
		<input type="hidden" name="filter_section" id="filter_section" value="<?php echo $this->state->get('filter.section'); ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
