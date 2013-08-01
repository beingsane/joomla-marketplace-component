<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Marketplace
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.path');
jimport('joomla.base.adapter');
jimport('joomla.utilities.arrayhelper');
JLoader::register('JMarketplaceAdapter', __DIR__.'/marketplaceadapter.php');

/**
 * Marketplace Class
 *
 * @package     Joomla.Platform
 * @subpackage  Store
 * @since       11.1
 */
class JMarketplace extends JAdapter
{
	/**
	 * @var    JMarketplace  JMarketplace instance container.
	 * @since  11.3
	 */
	protected static $instance;

	/**
	 * Constructor
	 *
	 * @since   11.1
	 */
	public function __construct()
	{
		// Adapter base path, class prefix
		parent::__construct(__DIR__, 'JMarketplace');
	}

	/**
	 * Returns a reference to the global Installer object, only creating it
	 * if it doesn't already exist.
	 *
	 * @return  object  An installer object
	 *
	 * @since   11.1
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new JMarketplace;
		}
		return self::$instance;
	}

	/**
	 * Finds an update for an tmpl
	 *
	 * @param   integer  $cacheTimeout  How many seconds to cache update information; if zero, force reload the update information
	 *
	 * @return  boolean True if there are updates
	 *
	 * @since   11.1
	 */
	public function findUpdates($cacheTimeout = 0)
	{

		$db = $this->getDBO();
		$retval = false;

		$query = $db->getQuery(true);
		$query->select('a.marketplace_repository_id, a.type, a.location, a.last_check_timestamp');
		$query->from('#__marketplace_repositories AS a');
		$query->where('a.published>=1');
		
		$db->setQuery($query);
		$results = $db->loadAssocList();
		$result_count = count($results);
		$now = time();
		for ($i = 0; $i < $result_count; $i++)
		{
			$result = &$results[$i];
			$this->setAdapter($result['type']);
			if (!isset($this->_adapters[$result['type']]))
			{
				// Ignore update sites requiring adapters we don't have installed
				continue;
			}
			if ($cacheTimeout > 0)
			{
				if (isset($result['last_check_timestamp']) && ($now - $result['last_check_timestamp'] <= $cacheTimeout))
				{
					// Ignore update sites whose information we have fetched within
					// the cache time limit
					$retval = true;
					continue;
				}
			}
			$update_result = $this->_adapters[$result['type']]->findUpdate($result);
			
			if (is_array($update_result))
			{
				if (array_key_exists('update_sites', $update_result) && count($update_result['update_sites']))
				{
					$results = JArrayHelper::arrayUnique(array_merge($results, $update_result['update_sites']));
					$result_count = count($results);
				}
				if (array_key_exists('updates', $update_result) && count($update_result['updates']))
				{
					for ($k = 0, $count = count($update_result['updates']); $k < $count; $k++)
					{
						$current_update = &$update_result['updates'][$k];
						$update = JTable::getInstance('marketplaceextensions');
						
						$extension = JTable::getInstance('extension');
						$uid = $update
							->find(
							array(
								'element' => strtolower($current_update->get('element')), 
								'type' => strtolower($current_update->get('type')),
								'client_id' => strtolower($current_update->get('client_id')),
								'folder' => strtolower($current_update->get('folder'))
							)
						);

						$eid = $extension
							->find(
							array(
								'element' => strtolower($current_update->get('element')), 
								'type' => strtolower($current_update->get('type')),
								'client_id' => strtolower($current_update->get('client_id')),
								'folder' => strtolower($current_update->get('folder'))
							)
						);
						if (!$uid)
						{
							// Set the tmpl id
							if ($eid)
							{
								// We have an installed tmpl, check the update is actually newer
								$extension->load($eid);
								$data = json_decode($extension->manifest_cache, true);
								if (version_compare($current_update->version, $data['version'], '>') == 1)
								{
									$current_update->extension_id = $eid;
									$current_update->store();
								}
							}
							else
							{
								// A potentially new tmpl to be installed
								$current_update->store();
							}
						}
						else
						{
							$update->load($uid);

							// If there is an update, check that the version is newer then replaces
							if (version_compare($current_update->version, $update->version, '>') == 1)
							{
								$current_update->store();
							}
						}
					}
				}
				$update_result = true;
			}
			elseif ($retval)
			{
				$update_result = true;
			}

			// Finally, update the last update check timestamp
			$query = $db->getQuery(true)
				->update($db->quoteName('#__marketplace_repositories'))
				->set($db->quoteName('last_check_timestamp') . ' = ' . $db->quote($now))
				->where($db->quoteName('marketplace_repository_id') . ' = ' . $db->quote($result['marketplace_repository_id']));
			$db->setQuery($query);
			$db->execute();
		}
		return $retval;
	}

	/**
	 * Finds an update for an tmpl
	 *
	 * @param   integer  $id  Id of the tmpl
	 *
	 * @return  mixed
	 *
	 * @since   11.1
	 */
	public function update($id)
	{
		$updaterow = JTable::getInstance('marketplaceextensions');
		$updaterow->load($id);
		$update = new JUpdate;
		if ($update->loadFromXML($updaterow->buttonurl))
		{
			return $update->install();
		}
		return false;
	}

}