<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$default_icon = '../media/com_marketplace/images/128x128_icon.gif';
?>
<table class="table table-striped table-bordered">
<?php $nr = 1; foreach ($this->items as $extension): ?>
    <?php
    if (empty($extension->icon)) {
        $extension->icon = $default_icon;
    }
    ?>
<?php if ($nr == 1): ?>
<tr>
<?php endif; ?>
<td>
    <div class="row-fluid">
		<div class="span3">
			<a href="index.php?option=com_marketplace&view=extension&id=<?php echo $extension->marketplace_extension_id; ?>"><img class="img-rounded" src="<?php echo $extension->icon; ?>" onerror="this.value='<?php echo $default_icon; ?>'" /></a>
		</div>
		<div class="span8">
			<a href="index.php?option=com_marketplace&view=extension&id=<?php echo $extension->marketplace_extension_id; ?>"><strong><?php echo $extension->name; ?></strong></a>
			<br />
			<?php echo $extension->author; ?>
			<br />
			<small><?php echo MarketplaceHelperRating::rating($extension->rating); ?> <i class='icon-comment'></i> <?php echo $extension->reviews;?></small>
			<br />
			<?php echo MarketplaceHelperButton::download($extension); ?>
		</div>
    </div>
</td>
<?php if ($nr == 4): ?>
</tr>
<?php $nr = 0; endif; ?>
<?php $nr++; endforeach; ?>
    <?php if ($nr > 1): ?>
<?php for (;$nr <= 4;$nr++): ?>
    <td><div class="row-fluid"><div class="span3"></div><div class="span8"></div></div></td>
<?php endfor; ?>
<?php if ($nr == 4): ?>
</tr>
<?php $nr = 0; endif; ?>
    <?php endif; ?>

</table>