<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Import library dependencies
jimport('joomla.application.component.modeladmin');

/**
 * Item Model for an Repository.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_marketplace
 * @since		3.1
 */
class MarketplaceModelRepository extends JModelAdmin
{
    /**
     * Method to get the record form.
     *
     * @param	array	$data		Data for the form.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     *
     * @return	mixed	A JForm object on success, false on failure
     * @since	3.1
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_marketplace.repositories', 'repository', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    /**
     * Returns a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     *
     * @return	JTable	A database object
     */
    public function getTable($type = 'marketplacerepositories', $prefix = 'JTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function importExtensions($marketplace_repository_id, $last_check_timestamp,$items)
    {
        $response = array('result' => true);

        foreach ($items as $extensionData) {
            $extensionData['marketplace_repository_id'] = $marketplace_repository_id;

            if (!empty($extensionData['description'])) {
                $extensionData['description'] = base64_decode($extensionData['description']);
            }

            if (empty($extensionData['client_id'])) {
                $extensionData['client_id'] = JFactory::getApplication('site')->getClientId();
            }

            // Only add the update if it is on the same platform and release as we are
            $ver = new JVersion;
            // Lower case and remove the exclamation mark
            $product = strtolower(JFilterInput::getInstance()->clean($ver->PRODUCT, 'cmd'));
            if (!isset($extensionData['targetplatform'])) {
                $extensionData['targetplatform'] = $product;
            }
            // Set this to ourself as a default
            if (!isset($extensionData['targetplatformversion'])) {
                $extensionData['targetplatformversion'] = $ver->RELEASE;
            }

            $current_update = JTable::getInstance('marketplaceextensions');
            $marketExtensions = JTable::getInstance('marketplaceextensions');
            $extension = JTable::getInstance('extension');

            $current_update->bind($extensionData);

            $uid = $marketExtensions->find(array(
                'identifier' => strtolower($extensionData['identifier']),
                'type' => strtolower($extensionData['type']),
                'client_id' => strtolower($extensionData['client_id']),
                'folder' => strtolower($extensionData['folder']),
                'sku' => strtolower($extensionData['sku'])
            ));

            $eid = $extension->find(array(
                'element' => strtolower($extensionData['identifier']),
                'type' => strtolower($extensionData['type']),
                'client_id' => strtolower($extensionData['client_id']),
                'folder' => strtolower($extensionData['folder'])
            ));
            if (!$uid)
            {
                // Set the extension_id id
                if ($eid)
                {
                    // We have an installed extension, check the update is actually newer
                    $extension->load($eid);
                    $data = json_decode($extension->manifest_cache, true);
                    if (version_compare($marketExtensions->version, $data['version'], '>') == 1)
                    {
                        $marketExtensions->extension_id = $eid;
                        $response['result'] = $current_update->store();
                    }
                }
                else
                {
                    // A potentially new extension to be installed
                    $response['result'] = $current_update->store();
                }
            }
            else
            {
                $extension->load($uid);

                // If there is an update, check that the version is newer then replaces
                if (version_compare($current_update->version, $marketExtensions->version, '>') == 1)
                {
                    $response['result'] = $current_update->store();
                }
            }

            if (!$response['result']) {
                return $response;
            }
        }

        if (!empty($last_check_timestamp)) {
            $repository = JTable::getInstance('marketplacerepositories');
            $repository->load($marketplace_repository_id);
            $repository->last_check_timestamp = $last_check_timestamp;
            $response['result'] = $repository->store();
        }

        return $response;
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
        $db = $this->parent->getDBO();
        $query = $db->getQuery(true)
            ->update('#__marketplace_repositories')
            ->set('enabled = 0')
            ->where('location = ' . $db->quote($url));
        $db->setQuery($query);
        $db->execute();

        JLog::add($text, JLog::WARNING, 'marketplace');
        $app = JFactory::getApplication();
        $app->enqueueMessage($text, 'warning');
        return false;
    }
}