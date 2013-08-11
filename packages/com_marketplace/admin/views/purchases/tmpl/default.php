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
$default_icon = '../media/com_marketplace/images/64x64_icon.gif';
?>
<?php if (!empty( $this->sidebar)) : ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
<?php else : ?>
    <div id="j-main-container">
<?php endif;?>
        <form action="<?php echo JRoute::_(JFactory::getUri());?>" method="post" name="adminForm" id="adminForm" class="">
        <div class="page-header">
            <h1><?php echo JText::_('COM_MARKETPLACE_TEXT_PURCHASES'); ?></h1>
            <div class="input-append">
                <input type="text" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" id="filter_search" placeholder="<?php echo JText::_('COM_MARKETPLACE_'.$this->getName().'_FILTER_SEARCH_DESC'); ?>" name="filter_search" class="input-xxlarge">
                <button class="btn" type="button"><i class="icon-search"></i></button>
                <button title="" onclick="document.id('filter_search').value='';this.form.submit();" type="button" class="btn hasTooltip" data-original-title="Clear"><i class="icon-remove"></i></button>
            </div>
        </div>


        <table class="table table-striped table-bordered">

 <?php foreach ($this->items as $i => $item): ?>
     <?php
     if (empty($extension->icon)) {
         $extension->icon = $default_icon;
     }
     ?>
            <tr>
                <td>
        <div class="row-fluid">
            <div class="span1">
                <img class="img-rounded" width="64px" src="<?php echo $item->icon; ?>" onerror="this.value=''" />
            </div>
            <div class="span9">
                <h3><?php echo $item->name; ?> <small><?php echo JText::sprintf('COM_MARKETPLACE_MARKETPLACE_EXTENSIONS_INFO', $item->author); ?></small></h3>
                <?php echo $item->purchased_date; ?>
            </div>
            <div class="span2">
                <?php echo MarketplaceHelperButton::download($item,' pull-right btn-large'); ?>
            </div>
        </div>
     </td>
            </tr>
    <?php endforeach; ?>

        </table>
        <div class="row-fluid">
            <div class="span12 pagination pagination-centered">
                <?php echo $this->pagination->getListFooter(); ?>
            </div>
        </div>
</div>
    <input type="hidden" name="option" id="option" value="com_marketplace" />
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>