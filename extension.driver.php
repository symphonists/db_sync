<?php

	require_once(EXTENSIONS . '/db_sync/lib/class.logquery.php');
	
	Class extension_db_sync extends Extension {
	
		public function about() {
			return array(
				'name'			=> 'Database Synchroniser',
				'version'		=> '0.9.1',
				'release-date'	=> '2011-02-10',
				'author'		=> array(
					'name'			=> 'Nick Dunn, Richard Warrender',
					'website'		=> 'http://airlock.com',
					'email'			=> 'nick.dunn@airlock.com'
				),
				'description'	=> 'Logs structural database changes to allow syncing between builds.'
			);
		}
		
		public function install() {
			Symphony::Configuration()->set('enabled', 'yes', 'db_sync');
			Administration::instance()->saveConfig();
			return true;
		}
		
		public function uninstall() {
			if (file_exists(MANIFEST . '/db_sync.sql')) unlink(MANIFEST . '/db_sync.sql');
			
			Symphony::Configuration()->remove('db_sync');
			Administration::instance()->saveConfig();
		}
		
		public static function addToLogFile($line) {
			$logfile = MANIFEST . '/db_sync.sql';
			$handle = @fopen($logfile, 'a');
			fwrite($handle, $line);
			fclose($handle);			
		}		

	}
	
?>