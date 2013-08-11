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
		foreach (MarketplaceHelper::getCollections() as $collection) {
			$html .= '<li>';
			$html .= '<a onclick="document.id(\'filter_section\').value=\''.$collection->value.'\';document.id(\'adminForm\').submit();" href="javascript:void(0);">';
			$html .= JText::sprintf('COM_MARKETPLACE_TEXT_BROWSE_SELECT',ucfirst($collection->text));
			$html .= '</a>';
			$html .= '</li>';
		}
		$html .= '</ul>';
		$html .= '</div>';
		return $html;
	}
	
	/**
	 * Build tmpl button
	 * 
	 * @param	Object $extension
	 * 
	 * @since	3.1
	 */
	static public function download($extension,$btn_class = '')
	{
		$button = $extension->plan;
		$href = 'javascript:void(0);';
        $target= '_self';
		if ($extension->extension_id == 0) {
            if ($extension->purchased && $extension->plan == 'buy') {
                $extension->plan = 'register';
                $button = $extension->plan;
            }
			switch (strtolower($extension->plan)) {
				case 'register':
					$btn_class .= ' btn-warning';
                    $href = $extension->item_url;
                    $target= '_blank';
					break;
				case 'buy':
					$btn_class .= ' btn-success';
                    $href = $extension->item_url;
                    $target= '_blank';
					break;
				case 'install':
					$btn_class .= ' btn-info';
                    $href = $extension->item_url;
					break;
				default:
					$btn_class .= '';
					break;
			}
		} else {
            if ($extension->update_id) {
                $btn_class .= ' btn-primary';
                $button = 'update';
                $href = "javascript:document.id('option').value='com_installer';document.id('cid').value='{$extension->update_id}';Joomla.submitbutton('update.update');";
            } else {
                $btn_class .= ' disabled';
                $button='installed';
            }
		}
		
		$html = '<a href="'.$href.'" target="'.$target.'" class="btn'.$btn_class.'">'.JText::_('COM_MARKETPLACE_MARKETPLACE_BUTTON_'.$button).'</a>';
		
		return $html;
	}
}