<?php
	
	require_once(TOOLKIT . '/class.administrationpage.php');
	require_once(EXTENSIONS . '/asdc/lib/class.asdc.php');
	require_once(dirname(__FILE__) . '/../lib/class.logviewer.php');
	
	Class ContentExtensionDB_SyncLog extends AdministrationPage{
	
		protected $driver;
		protected $env;
		
		function view(){			
			$log = new LogViewer();
			$log->display(LogViewer::MODE_ECHO);
		}
		
	}