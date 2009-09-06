<?php

class LogQuery {
	
	private function getEventId() {
		
		static $id = null;
		
		if(!$id) {
			$id = uniqid('', true);
			Administration::instance()->Database->query(
				sprintf(
					"-- db_sync_ignore
					INSERT INTO db_sync_events (`event`, `author_name`, `page`) VALUES('%s', '%s', '%s')",
					$id,
					Administration::instance()->Author->getFullName(),
					Administration::instance()->getCurrentPageURL()
				)
			);
		}
		return $id;
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
			Administration::instance()->Database->query(
				sprintf(
					"-- db_sync_ignore
					INSERT INTO db_sync (`sql`, `event`) VALUES('%s', '%s')",
					MySQL::cleanValue($query),
					self::getEventId()
				)
			);
		}
		
	}

}