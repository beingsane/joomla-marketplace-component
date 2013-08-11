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
<div class="span4 well well-small">
    <a href="index.php?option=com_marketplace&view=extension&id=<?php echo $extension->marketplace_extension_id; ?>"><img src="<?php echo $extension->thumbnail; ?>" class="img-rounded" /></a>
	<h2><?php echo $extension->name; ?> <small><?php echo JText::sprintf('COM_MARKETPLACE_'.$this->getName().'_EXTENSIONS_INFO', $extension->author); ?></small></h2>
	<small><?php echo MarketplaceHelperRating::rating($extension->rating); ?><div class="visible-phone"><?php echo JText::sprintf('COM_MARKETPLACE_'.$this->getName().'_REVIEWS_PHONE',$extension->reviews); ?></div><div class="visible-tablet visible-desktop"><?php echo JText::sprintf('COM_MARKETPLACE_'.$this->getName().'_REVIEWS_TABLET',$extension->reviews); ?></div></small>
	<br />
	<?php echo MarketplaceHelperButton::download($extension); ?>
	<br clear="all" />
</div>
<?php if ($nr == 3): ?>
</div>
<?php $nr = 0; endif; ?>
<?php $nr++; endforeach; ?>