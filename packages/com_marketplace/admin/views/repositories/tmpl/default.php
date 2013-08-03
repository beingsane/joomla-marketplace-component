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
<script type="text/javascript">
Joomla.submitbutton = function(task)
{
	if (task == 'repository.save')
	{
		repository_url = jQuery('#marketplace_repository_url').val();
		if (repository_url.match(/^(http|https)\:\/\/[a-z_\-.\/?=&0-9]*/) && repository_url != 'http://' && repository_url != 'https://') {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo JText::_('COM_MARKETPLACE_REPOSITORIES_FORM_VALIDATION_INVALID_REPOSITORY_URL'); ?>');
		}
	} else {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
</script>
<div id="installer-languages">
	<form action="<?php echo JRoute::_('index.php?option=com_marketplace&view=repositories');?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif;?>

		<?php if (count($this->items) || $this->escape($this->state->get('filter.search'))) : ?>
			<?php echo $this->loadTemplate('filter'); ?>
			<table class="table table-striped">
				<thead>
					<tr>
						<th width="20" class="nowrap hidden-phone">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th class="nowrap">
							<?php echo JHtml::_('grid.sort', 'COM_MARKETPLACE_HEADING_NAME', 'name', $listDirn, $listOrder); ?>
						</th>
						<th width="35%" class="nowrap hidden-phone">
							<?php echo JText::_('COM_MARKETPLACE_HEADING_DETAILS_URL'); ?>
						</th>
						<th width="1%" style="min-width:55px" class="nowrap center">
							<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
						</th>
						<th width="30" class="nowrap hidden-phone">
							<?php echo JHtml::_('grid.sort', 'COM_MARKETPLACE_HEADING_ID', 'store_repository_id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="6">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php foreach ($this->items as $i => $repository) :
				?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="hidden-phone">
							<?php if ($repository->published == 2 && !$this->user->authorise('repositories.manage.state','com_marketplace')): ?>
								<input type="checkbox" disabled="disabled" />
							<?php else: ?>
								<?php echo JHtml::_('grid.id', $i, $repository->marketplace_repository_id); ?>
							<?php endif; ?>
						</td>
						<td>
							<?php echo $repository->name; ?>
						</td>
						<td class="small hidden-phone">
							<?php echo $repository->location; ?>
						</td>
						<td class="center">
							<?php if (!$repository->published) : ?>
							<strong>X</strong>
							<?php else : ?>
								<?php echo JHtml::_('MarketplaceHtml.Manage.state', 'repositories', $this->getName(),$repository->published, $i, $repository->published < 2, 'cb'); ?>
							<?php endif; ?>
						</td>
						<td class="small hidden-phone">
							<?php echo $repository->marketplace_repository_id; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php else : ?>
			<div class="hero-unit">
			    <h3><?php echo JText::_('COM_MARKETPLACE'); ?></h3>
			    <p><?php echo JText::_('COM_MARKETPLACE_REPOSITORIES_INTRODUCTION'); ?></p>
			    <p>
			        <div class="input-append">
    <input name="jform[location]" id="marketplace_repository_url" value="http://" type="text">
    <button class="btn btn-success" onclick="Joomla.submitbutton('repository.save')" type="button"><?php echo JText::_('COM_MARKETPLACE_REPOSITORIES_BUTTON_ADD'); ?></button>
    </div>
			    </p>
			   </div>
		<?php endif; ?>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		<?php if (!empty($this->items)): ?>
			<br clear="all" />
			<?php if ($this->canDo->get('core.create')): ?>
			<fieldset class="uploadform">
				<legend><?php echo JText::_('COM_MARKETPLACE_REPOSITORIES_ADD_MANIFEST'); ?></legend>
				<label for="marketplace_repository_url"><?php echo JText::_('COM_MARKETPLACE_REPOSITORIES_MANIFEST_URL'); ?></label>
				<input type="text" value="http://" size="70" class="input_box" name="jform[location]" id="marketplace_repository_url" />
				<button onclick="Joomla.submitbutton('repository.save')" class="btn btn-success"><?php echo JText::_('COM_MARKETPLACE_REPOSITORIES_BUTTON_ADD'); ?></button>
			</fieldset>
			<?php endif; ?>
		<?php endif; ?>
		</div>
	</form>
</div>

