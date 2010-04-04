<?php

class LogQuery {
	
	public static $id = null;
	public static $previous_id = null;
	
	private function getEventId() {
		if(!self::$id) self::$id = md5(uniqid('', true));
		return self::$id;
	}
	
	static function log($query) {

		if (
			// ignore queries made by this extension
			!preg_match('/^-- db_sync_ignore/i', $query) &&
			// only structural changes, no SELECT
			preg_match('/^(insert|update|delete|create|drop)/i', $query) &&
			// discard unrequired tables
			!preg_match('/(sym_sessions|sym_cache|sym_authors)/i', $query) &&
			// discard content updates to tbl_entries (includes tbl_entries_fields_*)
			!(preg_match('/^(insert|delete)/i', $query) && preg_match('/(sym_entries)/i', $query))
		) {
			
			self::getEventId();
			
			$line = '';
			
			if (self::$id != self::$previous_id) {
				
				$author = Administration::instance()->Author;
				
				$line .= "\r\n" . '-- ' . date('Y-m-d H:i:s', time());
				
				if (isset($author)) {
					$line .= ', ' . $author->getFullName();
				}
				
				if (!is_null(Administration::instance()->getCurrentPageURL())) {
					$line .= ', ' . Administration::instance()->getCurrentPageURL();
				}
				
				$line .= "\r\n";
			}
			
			$line .= $query . "\r\n";
			
			self::$previous_id = self::$id;
			
			require_once(EXTENSIONS . '/db_sync/extension.driver.php');
			extension_db_sync::addToLogFile($line);
		}
		
	}

}