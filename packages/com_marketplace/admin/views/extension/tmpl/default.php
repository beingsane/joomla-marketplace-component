<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$pathway = explode('/',$this->item->pathway);
$default_thumbnail = '../media/com_marketplace/images/460x345_thumbnail.gif';
if (empty($this->item->thumbnail)) {
    $this->item->thumbnail = $default_thumbnail;
}
?>
<div class="row-fluid">

    <div class="span3">
        <img class="img-polaroid" src="<?php echo $this->item->thumbnail; ?>" onerror="this.value='<?php echo $default_thumbnail; ?>'" />
        <br />
        <br />
        <div class="row">
            <div class="span3">

            </div>
            <div class="span8 pull-right">
                <?php echo MarketplaceHelperButton::download($this->item,' btn-large btn-block'); ?>
            </div>
        </div>
        <br />
        <?php echo JText::_('COM_MARKETPLACE_TEXT_RATING'); ?>: <?php echo MarketplaceHelperRating::rating($this->item->rating); ?>
        <br />
        <?php echo JText::sprintf('COM_MARKETPLACE_TEXT_CATEGORY', end($pathway)); ?>
        <br />
        <?php echo JText::sprintf('COM_MARKETPLACE_TEXT_VERSION',$this->item->version); ?>

    </div>
    <div class="span9">
        <div class="page-header">
            <h1><?php echo $this->item->name; ?> <small><?php echo JText::sprintf('COM_MARKETPLACE_TEXT_BY_AUTHOR',$this->item->author); ?></small></h1>
        </div>
        <ul class="nav nav-pills">
            <li>
                <a href="<?php echo $this->item->details_url; ?>" target="_blank"><?php echo JText::_('COM_MARKETPLACE_TEXT_INFO_URL'); ?></a>
            </li>
            <li><a href="<?php echo $this->item->demo_url; ?>" target="_blank"><?php echo JText::_('COM_MARKETPLACE_TEXT_DEMO_URL'); ?></a></li>
            <li><a href="<?php echo $this->item->author_url; ?>" target="_blank"><?php echo JText::_('COM_MARKETPLACE_TEXT_AUTHOR_URL'); ?></a></li>
        </ul>
        <hr>
        <p><?php echo nl2br($this->item->description); ?></p>
        <br />



    </div>



</div>