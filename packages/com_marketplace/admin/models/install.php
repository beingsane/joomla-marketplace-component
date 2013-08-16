<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

/**
 * Extension Marketplace Model
 *
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 * @since       3.1
 */
class MarketplaceModelInstall extends JModelLegacy
{
    /**
     * Return extension item
     *
     * @since   3.1
     */
    public function getItem()
    {
        if (empty($this->item)) {
            $db     = JFactory::getDBO();
            $query  = $db->getQuery(true);
            $input = JFactory::getApplication()->input;
            $extension_id = $input->getString('id');

            // Select the required fields from the updates table
            $query->select('a.identifier, a.name');
            $query->from($db->quoteName('#__marketplace_extensions').' AS a');
            // Join installed extensions
            $query->select('e.extension_id');
            $query->join('LEFT', $db->quoteName('#__extensions').' AS e ON e.element = a.identifier');
            // Join updates extensions
            $query->select('u.update_id');
            $query->join('LEFT', $db->quoteName('#__updates').' AS u ON (u.element = a.identifier AND e.extension_id = u.extension_id)');
            $query->where('a.ref_id='.$db->quote($extension_id));
            $db->setQuery($query);
            $this->item = $db->loadObject();
        }

        return $this->item;
    }

    public function createRestorationFile($basename = null)
    {
        // Get a password
        $password = JUserHelper::genRandomPassword(32);
        $app = JFactory::getApplication();
        $app->setUserState('com_marketplace.password', $password);

        // Do we have to use FTP?
        $method = $app->input->get('method', 'direct');

        // Get the absolute path to site's root
        $siteroot = JPATH_SITE;

        // Get the package name
        $config = JFactory::getConfig();
        $tempdir = $config->get('tmp_path');
        $file = $tempdir . '/' . $basename;
        $siteroot = $tempdir . '/' . JFile::stripExt($basename);

        if (!is_dir($siteroot)) {
            mkdir($siteroot);
        }

        $filesize = @filesize($file);
        $app->setUserState('com_marketplace.password', $password);
        $app->setUserState('com_marketplace.filesize', $filesize);

        $data = "<?php\ndefined('_AKEEBA_RESTORATION') or die('Restricted access');\n";
        $data .= '$restoration_setup = array(' . "\n";
        $data .= <<<ENDDATA
	'kickstart.security.password' => '$password',
	'kickstart.tuning.max_exec_time' => '5',
	'kickstart.tuning.run_time_bias' => '75',
	'kickstart.tuning.min_exec_time' => '0',
	'kickstart.procengine' => '$method',
	'kickstart.setup.sourcefile' => '$file',
	'kickstart.setup.destdir' => '$siteroot',
	'kickstart.setup.restoreperms' => '0',
	'kickstart.setup.filetype' => 'zip',
	'kickstart.setup.dryrun' => '0'
ENDDATA;

        if ($method == 'ftp')
        {
            // Fetch the FTP parameters from the request. Note: The password should be
            // allowed as raw mode, otherwise something like !@<sdf34>43H% would be
            // sanitised to !@43H% which is just plain wrong.
            $ftp_host = $app->input->get('ftp_host', '');
            $ftp_port = $app->input->get('ftp_port', '21');
            $ftp_user = $app->input->get('ftp_user', '');
            $ftp_pass = $app->input->get('ftp_pass', '', 'default', 'none', 2);
            $ftp_root = $app->input->get('ftp_root', '');

            // Is the tempdir really writable?
            $writable = @is_writeable($tempdir);
            if ($writable)
            {
                // Let's be REALLY sure
                $fp = @fopen($tempdir . '/test.txt', 'w');
                if ($fp === false)
                {
                    $writable = false;
                }
                else
                {
                    fclose($fp);
                    unlink($tempdir . '/test.txt');
                }
            }

            // If the tempdir is not writable, create a new writable subdirectory
            if (!$writable)
            {
                $FTPOptions = JClientHelper::getCredentials('ftp');
                $ftp = JClientFtp::getInstance($FTPOptions['host'], $FTPOptions['port'], null, $FTPOptions['user'], $FTPOptions['pass']);
                $dest = JPath::clean(str_replace(JPATH_ROOT, $FTPOptions['root'], $tempdir . '/admintools'), '/');
                if (!@mkdir($tempdir . '/admintools'))
                {
                    $ftp->mkdir($dest);
                }
                if (!@chmod($tempdir . '/admintools', 511))
                {
                    $ftp->chmod($dest, 511);
                }

                $tempdir .= '/admintools';
            }

            // Just in case the temp-directory was off-root, try using the default tmp directory
            $writable = @is_writeable($tempdir);
            if (!$writable)
            {
                $tempdir = JPATH_ROOT . '/tmp';

                // Does the JPATH_ROOT/tmp directory exist?
                if (!is_dir($tempdir))
                {

                    JFolder::create($tempdir, 511);
                    JFile::write($tempdir . '/.htaccess', "order deny, allow\ndeny from all\nallow from none\n");
                }

                // If it exists and it is unwritable, try creating a writable admintools subdirectory
                if (!is_writable($tempdir))
                {
                    $FTPOptions = JClientHelper::getCredentials('ftp');
                    $ftp = JClientFtp::getInstance($FTPOptions['host'], $FTPOptions['port'], null, $FTPOptions['user'], $FTPOptions['pass']);
                    $dest = JPath::clean(str_replace(JPATH_ROOT, $FTPOptions['root'], $tempdir . '/admintools'), '/');
                    if (!@mkdir($tempdir . '/admintools'))
                    {
                        $ftp->mkdir($dest);
                    }
                    if (!@chmod($tempdir . '/admintools', 511))
                    {
                        $ftp->chmod($dest, 511);
                    }

                    $tempdir .= '/admintools';
                }
            }

            // If we still have no writable directory, we'll try /tmp and the system's temp-directory
            $writable = @is_writeable($tempdir);

            if (!$writable)
            {
                if (@is_dir('/tmp') && @is_writable('/tmp'))
                {
                    $tempdir = '/tmp';
                }
                else
                {
                    // Try to find the system temp path
                    $tmpfile = @tempnam("dummy", "");
                    $systemp = @dirname($tmpfile);
                    @unlink($tmpfile);

                    if (!empty($systemp))
                    {
                        if (@is_dir($systemp) && @is_writable($systemp))
                        {
                            $tempdir = $systemp;
                        }
                    }
                }
            }

            $data .= <<<ENDDATA
	,
	'kickstart.ftp.ssl' => '0',
	'kickstart.ftp.passive' => '1',
	'kickstart.ftp.host' => '$ftp_host',
	'kickstart.ftp.port' => '$ftp_port',
	'kickstart.ftp.user' => '$ftp_user',
	'kickstart.ftp.pass' => '$ftp_pass',
	'kickstart.ftp.dir' => '$ftp_root',
	'kickstart.ftp.tempdir' => '$tempdir'
ENDDATA;
        }

        $data .= ');';

        // Remove the old file, if it's there...
        $configpath = JPATH_COMPONENT_ADMINISTRATOR . '/restoration.php';
        if (JFile::exists($configpath))
        {
            JFile::delete($configpath);
        }

        // Write new file. First try with JFile.
        $result = JFile::write($configpath, $data);
        // In case JFile used FTP but direct access could help
        if (!$result)
        {
            if (function_exists('file_put_contents'))
            {
                $result = @file_put_contents($configpath, $data);
                if ($result !== false)
                {
                    $result = true;
                }
            }
            else
            {
                $fp = @fopen($configpath, 'wt');

                if ($fp !== false)
                {
                    $result = @fwrite($fp, $data);
                    if ($result !== false)
                    {
                        $result = true;
                    }
                    @fclose($fp);
                }
            }
        }

        $result = array(
            'return' => $result,
            'pass' => $password
        );

        return $result;
    }

    public function download()
    {
        $session = JFactory::getSession();
        $input = JFactory::getApplication()->input;
        $download_url = trim(base64_decode($input->getBase64('download_url', $session->get('marketplace.download_url'))));
        $tmp_file = $input->getCmd('tmp_file');

        if (substr($download_url,0,4)=='http') {
            $http_response_header = array_change_key_case(get_headers($download_url, 1),CASE_LOWER);
            if (!empty($http_response_header['content-disposition']))
            {
                preg_match('/filename=([^ ]+)/', $http_response_header['content-disposition'], $matches);
                $matches[1] = str_replace('"','',$matches[1]);
                $matches[1] = str_replace(';','',$matches[1]);
                $tmp_file = $matches[1];
            } else {
                $tmp_file = basename($download_url);
            }
        }

        $config = JFactory::getConfig();
        $tempdir = $config->get('tmp_path');
        $file_path = $tempdir.DIRECTORY_SEPARATOR.$tmp_file;
        file_put_contents($file_path,file_get_contents($download_url));

        $total_size = filesize($file_path);
        $response = array(
            'total_size' => $total_size,
            'read' => $total_size,
            'download_url' => base64_encode($download_url),
            'tmp_file' => $tmp_file,
            'file_path' => $tempdir.DIRECTORY_SEPARATOR.JFile::stripext($tmp_file)
        );

        return $response;
    }
}