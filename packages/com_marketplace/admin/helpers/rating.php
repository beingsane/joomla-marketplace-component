<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Rating component helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @since       3.1
 */
class MarketplaceHelperRating
{
	static public function rating($rating, $max = 5)
	{	
		$full = intval($rating);
		$decimal = explode('.',$rating);
		$each = (intval(end($decimal)) == 0) ? 0 : 1;
		$empty = intval($max - $rating);
		
		$html = str_repeat('<i class="icon-star"></i>', $full);
		$html .= str_repeat('<i class="icon-star-2"></i>',  $each);
		$html .= str_repeat('<i class="icon-star-empty"></i>', $empty);
		
		echo $html;
	}
}