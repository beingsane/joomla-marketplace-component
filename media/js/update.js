
function pingUpdate()
{
	var postData = {};
	var total = updater_sites.length;
	
	postData.option = 'com_marketplace';
	postData.task = 'repository.find';
	
	jQuery('#repo_name').html(updater_sites[currentIndex - 1].name);
	jQuery('#repo_url').html(updater_sites[currentIndex - 1].location);
	
	jQuery.ajax({
		dataType: 'json',
		url: 'index.php',
		data: postData,
		success: function (data) {
			percent = parseFloat(currentIndex / total) * 100;
			joomlaupdate_progress_bar.set(percent);
			
			if (currentIndex == total) {
				window.setTimeout(function(){ window.location = updater_return_url; },1000);
			} else {
				currentIndex++;
				window.setTimeout("pingUpdate()",1000);
			}
		}
	});
}

var joomlaupdate_progress_bar = null;
var currentIndex = 1;
window.onload =  function() {
	joomlaupdate_progress_bar = new Fx.ProgressBar(document.id('progress'));
	pingUpdate();
}