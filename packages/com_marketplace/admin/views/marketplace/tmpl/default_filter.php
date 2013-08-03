<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
?>
<div class="btn-toolbar" id="filter-bar">
	<div class="filter-search btn-group pull-left">
	<div class="input-append">
		
		<input type="text" title="<?php echo JText::_('COM_MARKETPLACE_'.$this->getName().'_FILTER_SEARCH_DESC'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" id="filter_search" placeholder="<?php echo JText::_('COM_MARKETPLACE_'.$this->getName().'_FILTER_SEARCH_DESC'); ?>" class="search-query" name="filter_search" style="border-right: 0;">
		<?php echo MarketplaceHelperButton::collection($this->section); ?>
		<button class="btn" type="submit"><i class="icon-search"></i></button>
	</div>
	</div>
	<div class="btn-group pull-left hidden-phone">
		<button title="" onclick="document.id('filter_search').value='';this.form.submit();" type="button" class="btn hasTooltip" data-original-title="Clear"><i class="icon-remove"></i></button>
	</div>
</div>
<div class="clearfix"> </div>
