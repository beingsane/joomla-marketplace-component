<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marketplace
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<?php echo JText::sprintf('COM_MARKETPLACE_TEXT_INSTALL_EXTENSION', $this->item->name); ?>
<div class="progress">
    <div class="bar" style="width: 0%;"></div>
</div>
<p id="status"></p>
<form method="post" id="adminForm" name="adminForm" action="index.php">
    <input type="hidden" name="option" value="com_installer" />
    <input type="hidden" name="task" value="install.install" />
    <input type="hidden" name="install_directory" id="install_directory" value="" />
    <input type="hidden" name="installtype" value="folder" />
    <?php echo JHtml::_('form.token'); ?>
</form>

<script>
var joomlaupdate_error_callback = dummy_error_handler;
var joomlaupdate_stat_inbytes = 0;
var joomlaupdate_stat_outbytes = 0;
var joomlaupdate_stat_files = 0;
var joomlaupdate_stat_percent = 0;
var joomlaupdate_factory = null;
var joomlaupdate_progress_bar = null;

/**
 * An extremely simple error handler, dumping error messages to screen
 *
 * @param error The error message string
 */
function dummy_error_handler(error)
{
    alert("ERROR:\n"+error);
}

/**
 * Performs an AJAX request and returns the parsed JSON output.
 *
 * @param data An object with the query data, e.g. a serialized form
 * @param successCallback A function accepting a single object parameter, called on success
 * @param errorCallback A function accepting a single string parameter, called on failure
 */
function doAjax(data, successCallback, errorCallback)
{
    var json = JSON.stringify(data);
    if ( joomlaupdate_password.length > 0 )
    {
        json = AesCtr.encrypt( json, joomlaupdate_password, 128 );
    }
    var post_data = 'json='+encodeURIComponent(json);


    var structure =
    {
        success: function(msg)
        {
            // Initialize
            var junk = null;
            var message = "";

            // Get rid of junk before the data
            var valid_pos = msg.indexOf('###');
            if ( valid_pos == -1 ) {
                // Valid data not found in the response
                msg = 'Invalid AJAX data:\n' + msg;
                if (joomlaupdate_error_callback != null)
                {
                    joomlaupdate_error_callback(msg);
                }
                return;
            } else if( valid_pos != 0 ) {
                // Data is prefixed with junk
                junk = msg.substr(0, valid_pos);
                message = msg.substr(valid_pos);
            }
            else
            {
                message = msg;
            }
            message = message.substr(3); // Remove triple hash in the beginning

            // Get of rid of junk after the data
            var valid_pos = message.lastIndexOf('###');
            message = message.substr(0, valid_pos); // Remove triple hash in the end
            // Decrypt if required
            if ( joomlaupdate_password.length > 0 )
            {
                try {
                    var data = JSON.parse(message);
                } catch(err) {
                    message = AesCtr.decrypt(message, joomlaupdate_password, 128);
                }
            }

            try {
                var data = JSON.parse(message);
            } catch(err) {
                var msg = err.message + "\n<br/>\n<pre>\n" + message + "\n</pre>";
                if (joomlaupdate_error_callback != null)
                {
                    joomlaupdate_error_callback(msg);
                }
                return;
            }

            // Call the callback function
            successCallback(data);
        }
    };

    structure.type = 'POST';
    structure.url = joomlaupdate_ajax_url;
    structure.data = post_data;
    jQuery.ajax(structure);
}

/**
 * Pings the update script (making sure its executable!!)
 * @return
 */
function pingUpdate()
{
    // Reset variables
    joomlaupdate_stat_files = 0;
    joomlaupdate_stat_inbytes = 0;
    joomlaupdate_stat_outbytes = 0;

    // Do AJAX post
    var post = {task : 'ping'};
    doAjax(post, function(data){
        startUpdate(data);
    });
}

/**
 * Starts the update
 * @return
 */
function startUpdate()
{
    // Reset variables
    joomlaupdate_stat_files = 0;
    joomlaupdate_stat_inbytes = 0;
    joomlaupdate_stat_outbytes = 0;

    var post = { task : 'startRestore' };
    doAjax(post, function(data){
        processUpdateStep(data);
    });
}

/**
 * Steps through the update
 * @param data
 * @return
 */
function processUpdateStep(data)
{
    if (data.status == false)
    {
        if (joomlaupdate_error_callback != null)
        {
            joomlaupdate_error_callback(data.message);
        }
    }
    else
    {
        if (data.done)
        {
            joomlaupdate_factory = data.factory;
            jQuery('div.bar').width('100%');
            jQuery('#status').html('<?php echo JText::_('COM_MARKETPLACE_'.$this->getName().'_STEP_INSTALL'); ?>');
            jQuery('#install_directory').val(joomlaupdate_filepath);
            document.adminForm.submit();
        }
        else
        {
            // Add data to variables
            joomlaupdate_stat_inbytes += data.bytesIn;
            joomlaupdate_stat_percent = (joomlaupdate_stat_inbytes / joomlaupdate_totalsize) * 100;
            joomlaupdate_stat_outbytes += data.bytesOut;
            joomlaupdate_stat_files += data.files;

            // Display data
            jQuery('div.bar').width(joomlaupdate_stat_percent+'%');

            // Do AJAX post
            post = {
                task: 'stepRestore',
                factory: data.factory
            };
            doAjax(post, function(data){
                processUpdateStep(data);
            });
        }
    }
}
function extract(data)
{
    joomlaupdate_filepath = data.file_path;
    jQuery.ajax(
        {
            url: 'index.php?option=com_marketplace&task=install.prepareextraction&format=json',
            data: data,
            dataType: 'json',
            success: function(response) {
               if (response.return == true) {
                   joomlaupdate_password = response.pass;
                   pingUpdate();
               } else {
                   alert('error when prepare extraction');
               }
            }
        }
    );
}
function download(data)
{
    jQuery.ajax(
        {
            url: 'index.php?option=com_marketplace&task=install.download&format=json',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.read < response.total_size) {
                    percent = (response.read / response.total_size) / 100;
                    jQuery('div.bar').width(percent+'%');
                    download({download_url: response.download_url});
                } else {
                    percent = (response.read / response.total_size) / 100;
                    joomlaupdate_totalsize = response.total_size;
                    jQuery('#status').html('<?php echo JText::_('COM_MARKETPLACE_'.$this->getName().'_STEP_EXTRACT'); ?>');
                    extract(response);
                }
            }
        }
    );
}
window.onload = function() {
    jQuery('#status').html('<?php echo JText::_('COM_MARKETPLACE_'.$this->getName().'_STEP_DOWNLOAD'); ?>');
    download({identifier: "<?php echo $this->item->identifier; ?>",download_url: "<?php echo $this->download_url; ?>"});
}
</script>