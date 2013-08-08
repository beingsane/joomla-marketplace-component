<?php if (!empty( $this->sidebar)) : ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
<?php else : ?>
    <div id="j-main-container">
<?php endif;?>
        <div class="page-header">
            <h1><?php echo JText::_('COM_MARKETPLACE_TEXT_PURCHASES'); ?></h1>
        </div>
 <?php foreach ($this->items as $i => $item): ?>
        <div class="row-fluid">
            <div class="span1">
                <img class="img-rounded" width="64px" src="<?php echo $item->icon; ?>" />
            </div>
            <div class="span6">
                <h3><?php echo $item->name; ?> <small><?php echo JText::sprintf('COM_MARKETPLACE_MARKETPLACE_EXTENSIONS_INFO', $item->author); ?></small></h3>
                <?php echo $item->purchased_date; ?>
            </div>
            <div class="span4">
                <?php echo MarketplaceHelperButton::download($item); ?>
            </div>
        </div>
    <?php endforeach; ?>
        <div class="row-fluid">
            <div class="span12 pagination pagination-centered">
                <?php echo $this->pagination->getListFooter(); ?>
            </div>
        </div>
</div>