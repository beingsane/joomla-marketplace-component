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
<?php $nr = 1; foreach ($this->items as $extension): ?>
<?php if ($nr == 1): ?>
<div class="row-fluid">
<?php endif; ?>
<div class="span3 well well-small">
		<div class="span3">
			<img class="img-rounded" src="<?php echo $extension->image; ?>" />
		</div>
		<div class="span6">
			<strong><?php echo $extension->name; ?></strong>
			<br />
			<i class="icon-user"></i><?php echo JText::sprintf('COM_MARKETPLACE_'.$this->getName().'_EXTENSIONS_INFO', $extension->author); ?>
			<br />
			<small><?php echo MarketplaceHelperRating::rating($extension->rating); ?><div class="visible-phone"><?php echo JText::sprintf('COM_MARKETPLACE_'.$this->getName().'_REVIEWS_PHONE',$extension->reviews); ?></div><div class="visible-tablet visible-desktop"><?php echo JText::sprintf('COM_MARKETPLACE_'.$this->getName().'_REVIEWS_TABLET',$extension->reviews); ?></div></small>
			<br />
			<?php echo MarketplaceHelperButton::download($extension); ?>
		</div>
		<br clear="all" />
</div>
<?php if ($nr == 4): ?>
</div>
<?php $nr = 0; endif; ?>
<?php $nr++; endforeach; ?>