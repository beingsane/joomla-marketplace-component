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
 * Extension Button component helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @since       3.1
 */
class MarketplaceHelperButton
{
	/**
	 * List of collections
	 * 
	 * @var		Array
	 * 
	 * @since 	3.1
	 */
	static public $collections = array(
		'extensions' => '',
		'templates' => 'template',
		'translations' => 'language'
	);

	/**
	 * Build collection list
	 * 
	 * @since	3.1
	 */
	static public function collection($selected)
	{
		$html = '<div class="btn-group">';
		$html .= '<button data-toggle="dropdown" class="btn dropdown-toggle" style="border-radius: 0; border-left: 1px dotted ; border-right: 1px dotted; background: none; color: #555555;">';
		$html .=  JText::sprintf('COM_MARKETPLACE_TEXT_BROWSE_SELECT',$selected);
		$html .= '<span class="caret"></span>';
		$html .= '</button>';
		$html .= '<ul class="dropdown-menu">';
		foreach (self::$collections as $collection => $extension) {
			$html .= '<li>';
			$html .= '<a onclick="document.id(\'filter_browse\').value=\''.$extension.'\';document.id(\'adminForm\').submit();" href="javascript:void(0);">';
			$suffix = empty($extension) ? 'EXTENSIONS' : strtoupper($extension);
			$html .= JText::sprintf('COM_MARKETPLACE_TEXT_BROWSE_SELECT',JText::_('COM_MARKETPLACE_COLLECTION_'.$suffix ));
			$html .= '</a>';
			$html .= '</li>';
		}
		$html .= '</ul>';
		$html .= '</div>';
		return $html;
	}
	
	/**
	 * Build extension button
	 * 
	 * @param	Object $extension
	 * 
	 * @since	3.1
	 */
	static public function download($extension)
	{	
		$button = $extension->plan;
		if ($extension->extension_id == 0) {
			switch (strtolower($extension->plan)) {
				case 'register':
					$btn_class = ' btn-warning';
					break;
				case 'buy':
					$btn_class = ' btn-success';
					break;
				case 'install':
					$btn_class = ' btn-primary';
					break;
				default:
					$btn_class = '';
					break;
			}
		} else {
			$btn_class = ' disabled';
			$button='installed';
		}
		
		$html = '<button type="button" class="btn'.$btn_class.'">'.JText::_('COM_MARKETPLACE_MARKETPLACE_BUTTON_'.$button).'</button>';
		
		return $html;
	}
}