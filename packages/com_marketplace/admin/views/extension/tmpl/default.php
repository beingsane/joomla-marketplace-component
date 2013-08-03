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
        <div class="page-header">
            <h1><?php echo $this->item->name; ?></h1>
            <small>
                   <?php echo JText::sprintf('COM_MARKETPLACE_TEXT_BY_AUTHOR',$this->item->author); ?>
                   <br />
                   <?php echo JText::sprintf('COM_MARKETPLACE_TEXT_VERSION',$this->item->version); ?>
            </small>
        </div>


            <img class="img-polaroid" src="<?php echo $this->item->icon; ?>" />
            <br />
            <small><?php echo MarketplaceHelperRating::rating($this->item->rating); ?><i class='icon-comment'></i> <?php echo $this->item->reviews;?></small>
            <br />
            <?php echo nl2br($this->item->description); ?>
            <br />

            <?php if (count($this->item->images) > 0): ?>
                <div class="row-fluid">
                    <ul class="thumbnails">
                        <?php foreach ($this->item->images as $image): ?>
                            <li class="span<?php echo floor(12 / count($this->item->images)); ?>">
                                <a class="thubmail">
                                    <img class="img-polaroid" src="<?php echo $image; ?>" />
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>


            <a href="<?php echo $this->item->details_url; ?>" target="_blank" class="btn"><?php echo JText::_('COM_MARKETPLACE_TEXT_INFO_URL'); ?></a>
            <a href="<?php echo $this->item->author_url; ?>" target="_blank" class="btn"><?php echo JText::_('COM_MARKETPLACE_TEXT_AUTHOR_URL'); ?></a>
            <?php echo MarketplaceHelperButton::download($this->item); ?>

    </div>
</div>