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
class JTableMarketplaceextensions extends JTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__marketplace_extensions', 'marketplace_extension_id', $db);
    }

    /**
     * Overloaded check function
     *
     * @return  boolean  True if the object is ok
     *
     * @see     JTable::check
     * @since   3.1
     */
    public function check()
    {
        // Check for valid name
        if (trim($this->name) == '' || trim($this->identifier) == '')
        {
            $this->setError(JText::_('JLIB_DATABASE_ERROR_MUSTCONTAIN_A_TITLE_EXTENSION'));
            return false;
        }

        if (trim($this->author) == '')
        {
            $this->setError(JText::_('COM_MARKETPLACE_EXTENSION_ERROR_MUSTCONTAIN_A_AUTHOR'));
            return false;
        }

        return true;
    }

    /**
     * Set primary key
     *
     * @param   $id
     *
     * @since   3.1
     */
    public function setKey($id)
    {
        $key = $this->_tbl_key;
        $this->$key = $id;
    }

    /**
     * Overloaded bind function
     *
     * @param   array  $array   Named array
     * @param   mixed  $ignore  An optional array or space separated list of properties
     *                          to ignore while binding.
     *
     * @return  mixed  Null if operation was satisfactory, otherwise returns an error
     *
     * @see     JTable::bind
     * @since   3.1
     */
    public function bind($array, $ignore = '')
    {
        if (isset($array['tags']) && is_array($array['tags']))
        {
            $registry = new JRegistry;
            $registry->loadArray($array['tags']);
            $array['tags'] = (string) $registry;
        }

        if (isset($array['gallery']) && is_array($array['gallery']))
        {
            $registry = new JRegistry;
            $registry->loadArray($array['gallery']);
            $array['gallery'] = (string) $registry;
        }

        return parent::bind($array, $ignore);
    }

    /**
     * Method to create and execute a SELECT WHERE query.
     *
     * @param   array  $options  Array of options
     *
     * @return  string  Results of query
     *
     * @since   3.1
     */
    public function find($options = array())
    {
        $where = array();
        foreach ($options as $col => $val)
        {
            if (JStringInflector::getInstance()->isPlural($col)) {
                $where[] = $col . ' LIKE ' . $this->_db->quote('%' . $this->_db->escape($val, true) . '%');
            } else {
                $where[] = $col . ' = ' . $this->_db->quote($val);
            }

        }
        $query = $this->_db->getQuery(true)
            ->select($this->_db->quoteName($this->_tbl_key))
            ->from($this->_db->quoteName($this->_tbl))
            ->where(implode(' AND ', $where));
        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }
}