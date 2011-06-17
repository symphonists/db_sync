<?php

class LogQuery {
	
	public static $meta_written = FALSE;
	
	static function log($query) {
		// prevent execution on the frontend
		if(!class_exists('Administration')) return;
		//if(!Symphony::Engine() instanceOf Administration) return;
		
		if(Symphony::Configuration()->get('enabled', 'db_sync') == 'no') return;
		
		$tbl_prefix = Symphony::Configuration()->get('tbl_prefix', 'database');

		/* FILTERS */
		// only structural changes, no SELECT statements
		if (!preg_match('/^(insert|update|delete|create|drop|alter|rename)/i', $query)) return;
		// un-tracked tables (sessions, cache, authors)
		if (preg_match("/{$tbl_prefix}(authors|cache|forgotpass|sessions)/i", $query)) return;
		// content updates in tbl_entries (includes tbl_entries_fields_*)
		if (preg_match('/^(insert|delete|update)/i', $query) && preg_match("/({$config->tbl_prefix}entries)/i", $query)) return;
		
		$line = '';
		
		if(self::$meta_written == FALSE) {
			
			$line .= "\n" . '-- ' . date('Y-m-d H:i:s', time());
			
			$author = Administration::instance()->Author;
			if (isset($author)) $line .= ', ' . $author->getFullName();
			
			$url = Administration::instance()->getCurrentPageURL();
			if (!is_null($url)) $line .= ', ' . $url;
			
			$line .= "\n";
			
			self::$meta_written = TRUE;
			
		}
		
		$query = trim($query);
		
		// append query delimeter if it doesn't exist
		if (!preg_match('/;$/', $query)) $query .= ";";
		
		$line .= $query . "\n";
		
		require_once(EXTENSIONS . '/db_sync/extension.driver.php');
		extension_db_sync::addToLogFile($line);
		
	}

}