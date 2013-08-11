function updateRepository(url, data) {
    jQuery.ajax({
        cache: false,
        dataType: 'jsonp',
        url: url,
        data: data,
        success: function (software){
            if (software.items.length == 0) {
                updateExtensionProgress(software);
                currentRepositoryIndex++;
                window.setTimeout("checkQueue()",1000);
            } else {
                jQuery.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: 'index.php?option=com_marketplace&task=repository.find',
                    data: {marketplace_repository_id: repositories[currentRepositoryIndex].marketplace_repository_id, timestamp: software.repository.updated_date,json: JSON.encode(software.items)},
                    success: function (marketplace) {
                        if (marketplace.result) {
                            if (software.repository.pages > software.repository.page) {
                                updateExtensionProgress(software)
                                var nextPage = parseInt(software.repository.page) + 1;
                                updateRepository(url,{page: nextPage, rv: rel_version, timestamp: repositories[currentRepositoryIndex].last_check_timestamp});
                            } else {
                                updateExtensionProgress(software);
                                currentRepositoryIndex++;
                                window.setTimeout("checkQueue()",1000);
                            }
                        }
                    },
                    error: function() {
                        currentRepositoryIndex++;
                        window.setTimeout("checkQueue()",1000);
                    }
                });
            }
        },
        error: function (resopnse) {
            currentRepositoryIndex++;
            window.setTimeout("checkQueue()",1000);
        }
    });
}

function updateRepositoryProgress()
{
    if (currentRepositoryIndex == repositories.length) return;
    jQuery('#repo_name').html(repositories[currentRepositoryIndex].name);
    jQuery('#repo_url').html(repositories[currentRepositoryIndex].location);
}

function updateExtensionProgress(software)
{
    var percent = Math.ceil( (parseFloat(software.repository.page / software.repository.pages) * 100) / repositories.length );
    repositoryProgressbar.set( parseFloat(document.id('repositories').getAttribute('value')) + percent);
}

function checkQueue()
{
    if (repositories.length == currentRepositoryIndex) {
        window.setTimeout(function(){ window.location = updater_return_url; },1000);
    } else {
        updateRepositoryProgress();
        updateRepository(repositories[currentRepositoryIndex].location,{page: 1, rv: rel_version, timestamp: repositories[currentRepositoryIndex].last_check_timestamp});
    }
}
var repositoryProgressbar = null;
var currentRepositoryIndex = 0;
window.onload =  function() {
    repositoryProgressbar = new Fx.ProgressBar(document.id('repositories'));
    checkQueue();
}
