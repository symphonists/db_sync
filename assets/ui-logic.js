jQuery(document).ready(function() {
	jQuery(".settings button[name='action[db_sync_flush]']").click(function() {
		return confirm('Flusing the log will erase all log data. Are you sure?');
	});
});