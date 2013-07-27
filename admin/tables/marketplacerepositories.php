<?php

/**
 * @version     1.0.0
 * @package     com_store
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      julio <juliopfneto@gmail.com> - http://www.jconnect.me
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
        parent::__construct('#__marketplace_repositories', 'store_repository_id', $db);
    }

    public function check()
    {
    	if (empty($this->location)) {
    		return false;
    	}

    	$http = new JHttp();
    	$response  = $http->get($this->location);
    	
    	$xml = simplexml_load_string($response->body);
    	
    	if (!$xml) {
    		return false;
    	}
    	
    	if (isset($xml->extension)) {
    		$this->type = 'collection';
    		$this->name = (string)$xml['name'];
    	} else {
    		return false;
    	}
    	
    	if ($this->name) {
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