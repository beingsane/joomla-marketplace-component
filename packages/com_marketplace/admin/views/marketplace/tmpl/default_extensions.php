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
			<a href="index.php?option=com_marketplace&view=extension&id=<?php echo $extension->marketplace_extension_id; ?>"><img class="img-rounded" src="<?php echo $extension->icon; ?>" /></a>
		</div>
		<div class="span6">
			<a href="index.php?option=com_marketplace&view=extension&id=<?php echo $extension->marketplace_extension_id; ?>"><strong><?php echo $extension->name; ?></strong></a>
			<br />
			<i class="icon-user"></i><?php echo JText::sprintf('COM_MARKETPLACE_'.$this->getName().'_EXTENSIONS_INFO', $extension->author); ?>
			<br />
			<small><?php echo MarketplaceHelperRating::rating($extension->rating); ?> <i class='icon-comment'></i> <?php echo $extension->reviews;?></small>
			<br />
			<?php echo MarketplaceHelperButton::download($extension); ?>
		</div>
		<br clear="all" />
</div>
<?php if ($nr == 4): ?>
</div>
<?php $nr = 0; endif; ?>
<?php $nr++; endforeach; ?>