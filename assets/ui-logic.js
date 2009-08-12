jQuery(document).ready(function() {

	var wipeButton = jQuery(".settings button[name='action[db_sync_flush]']");
	wipeButton.click(function() {
		return confirm('Flusing the log will erase all log data. Are you sure?');
	});
});