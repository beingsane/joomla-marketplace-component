<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Repositories Table class
 */
class JTableMarketplacerepositories extends JTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__marketplace_repositories', 'marketplace_repository_id', $db);
    }

    /**
     * Repository error reporting
     *
     * @param   $url   The string url
     * @param   $text  The reason of error
     *
     * @return  False
     *
     * @since   3.1
     */
    public function error($url, $text)
    {
        $db = $this->_db;
        $query = $db->getQuery(true)
            ->update('#__marketplace_repositories')
            ->set('published = 0')
            ->where('location = ' . $db->quote($url));
        $db->setQuery($query);
        $db->execute();

        JLog::add($text, JLog::WARNING, 'marketplace');
        JFactory::getApplication()->enqueueMessage($text, 'warning');
        return false;
    }

    /**
     * Check repository server
     *
     * @return bool|False
     */
    public function check()
    {
    	if (empty($this->location)) {
    		return false;
    	}

        $key = $this->_tbl_key;

        if (!$this->$key) {
            $site_uri = JUri::getInstance()->base();
            $http = new JHttp();
            $callback_function = 'mp';
            $response  = $http->get($this->location,array('referer' => $site_uri, 'callback' => $callback_function));
            if (200 != $response->code)
            {
                return $this->error($this->location, JText::sprintf('COM_MARKETPLACE_REPOSITORY_OPEN_URL', $url));
            }
            
            if (strpos($response->body,$callback_function.'(') === false) {
                return $this->error($this->location, JText::_('COM_MARKETPLACE_REPOSITORY_INVALID_RESPONSE'));
            }
            
            $data = json_decode($response->body, true);

            if (is_null($data)) {
                return $this->error($this->location, JText::_('COM_MARKETPLACE_REPOSITORY_INVALID_RESPONSE'));
            }

            $this->name = $data['repository']['name'];
        }
    	
    	if ($this->location) {
    		$key = $this->_tbl_key;
    		$query = $this->_db->getQuery(true);
    		$query->select($this->_tbl_key);
    		$query->from($this->_tbl);
    		$query->where('location='.$this->_db->quote($this->location));
    		$this->_db->setQuery($query);
    		$this->$key = $this->_db->loadResult();
    	}
    	
    	if ($this->$key == 0) {
    		$this->published = 1;
    	}
    	
    	return true;    	
    }
}
