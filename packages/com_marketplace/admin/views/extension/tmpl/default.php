<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div class="row-fluid">
    <div class="span12 well well-small">

            <img class="img-polaroid" src="<?php echo $this->item->icon; ?>" />

            <h2><?php echo $this->item->name; ?></h2>
            <br />
            <i class="icon-user"></i><?php echo JText::sprintf('COM_MARKETPLACE_'.$this->getName().'_EXTENSIONS_INFO', $this->item->author); ?>
            <br />
            <small><?php echo MarketplaceHelperRating::rating($this->item->rating); ?><div class="visible-phone"><?php echo JText::sprintf('COM_MARKETPLACE_'.$this->getName().'_REVIEWS_PHONE',$this->item->reviews); ?></div><div class="visible-tablet visible-desktop"><?php echo JText::sprintf('COM_MARKETPLACE_'.$this->getName().'_REVIEWS_TABLET',$this->item->reviews); ?></div></small>
            <br />
            <?php echo nl2br($this->item->description); ?>
            <br />

            <?php if (count($this->item->images) > 0): ?>
            <?php foreach ($this->item->images as $image): ?>
                <img class="img-polaroid" src="<?php echo $image; ?>" />
            <?php endforeach; ?>
            <?php endif; ?>


            <a href="<?php echo $this->item->details_url; ?>" target="_blank" class="btn"><?php echo JText::_('COM_MARKETPLACE_TEXT_INFO_URL'); ?></a>
            <a href="<?php echo $this->item->author_url; ?>" target="_blank" class="btn"><?php echo JText::_('COM_MARKETPLACE_TEXT_AUTHOR_URL'); ?></a>
            <?php echo MarketplaceHelperButton::download($this->item); ?>

    </div>
</div>