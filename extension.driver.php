<?php
	
	Class extension_db_sync extends Extension {
		
		public static $meta_written = FALSE;
		
		public function getSubscribedDelegates() {
			return array(
				array(
					'page'		=> '/backend/',
					'delegate'	=> 'PostQueryExecution',
					'callback'	=> 'log'
				)
			);
		}
		
		public function install() {
			Symphony::Configuration()->set('enabled', 'yes', 'db_sync');
			Administration::instance()->saveConfig();
			return TRUE;
		}
		
		public function uninstall() {
			if (file_exists(MANIFEST . '/db_sync.sql')) unlink(MANIFEST . '/db_sync.sql');
			Symphony::Configuration()->remove('db_sync');
			Administration::instance()->saveConfig();
		}
		
		public static function log($context) {
			if(Symphony::Configuration()->get('enabled', 'db_sync') == 'no') return;
			
			$query = $context['query'];

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
			
			$logfile = MANIFEST . '/db_sync.sql';
			$handle = @fopen($logfile, 'a');
			fwrite($handle, $line);
			fclose($handle);
			
		}

	}