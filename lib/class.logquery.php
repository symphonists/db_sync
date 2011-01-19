<?php

class LogQuery {
	
	public static $meta_written = FALSE;
	
	static function log($query) {

		$config = (object)Symphony::$Configuration->get('database');

		/* FILTERS */
		// queries produced by this extension are prefixed with this comment for filtering
		if (preg_match('/^-- db_sync_ignore/i', $query)) return;		
		// only structural changes, no SELECT
		if (!preg_match('/^(insert|update|delete|create|drop)/i', $query)) return;
		// un-tracked tables (sessions, cache, authors)
		if (preg_match("/({$config->tbl_prefix}sessions|{$config->tbl_prefix}cache|{$config->tbl_prefix}authors)/i", $query)) return;
		// content updates in tbl_entries (includes tbl_entries_fields_*)
		if (preg_match('/^(insert|delete|update)/i', $query) && preg_match("/({$config->tbl_prefix}entries)/i", $query)) return;
		
		$line = '';
		
		if(self::$meta_written == FALSE) {
			
			$line .= "\n\n" . '-- ' . date('Y-m-d H:i:s', time());
			
			$author = Administration::instance()->Author;
			if (isset($author)) $line .= ', ' . $author->getFullName();
			
			$url = Administration::instance()->getCurrentPageURL();
			if (!is_null($url)) $line .= ', ' . $url;
			
			self::$meta_written = TRUE;
			
		}
		
		$query = trim($query);
		if (!preg_match('/;$/', $query)) $query .= ";";
		
		$line .= "\n\n" . $query . "\n\n";
		
		require_once(EXTENSIONS . '/db_sync/extension.driver.php');
		extension_db_sync::addToLogFile($line);
		
	}

}