<?php

class LogViewer {
	
	const MODE_DOWNLOAD = "download";
	const MODE_ECHO = "echo";

	public function display($mode){

		ob_start();
	
		echo "--\n";
		echo "-- DB Sync from " . $_SERVER['HTTP_HOST'] . ' at ' . gmdate('D, d M Y H:i:s') . " GMT\n";
		echo "-- " . $this->countQueries() . " from " . $this->countEvents() . "\n";
		echo "--\n\n";
				
		try {
			$this->__printDump();
		} catch(Exception $e) {
			ob_end_clean();
			print_r(mysql_error());
			die();
		}

		switch($mode) {
				
			case self::MODE_DOWNLOAD:
				$contents = gzencode(ob_get_contents(), 9); // Gzipped
				ob_end_clean();
				$this->__outputHeader();
				echo $contents;
				break;
				
			case self::MODE_ECHO:
				header('Content-Type: text/plain');
				ob_end_flush();
				break;
				
			default:
				die('Unsupported Mode');
		}
		exit;
	}

	protected function __outputHeader() {
		$filtered = array(	'hostname' =>	str_replace('.', '_', $_SERVER['HTTP_HOST']),
							'date' =>		date('Ymd-His'));
						
		header('Content-type: application/x-gzip');	
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	    header('Content-disposition: attachment; filename=' . sprintf('%s-%s-%s.sql.gz', 'dbsync', $filtered['hostname'], $filtered['date']));
	    header('Pragma: no-cache');
	}

	protected function __printDump() {
		
		$query_events = Administration::instance()->Database->fetch('SELECT SQL_NO_CACHE * FROM `db_sync_events` ORDER BY ID ASC');
		
		$count = 1;
		foreach($query_events as $event) {
			
			$queries = Administration::instance()->Database->fetch("SELECT SQL_NO_CACHE e.id, e.* FROM `db_sync` AS `e` WHERE `event` = '{$event['event']}' ORDER BY ID ASC");
			
			echo "-- Event #{$count} ({$this->formatQueryCount(count($queries))})\n";
			echo "-- " . $event['author_name'] . ' at ' . date('D, d M Y H:i:s', strtotime($event['timestamp'])) . "\n";
			
			$description = '';
			if (preg_match('/blueprints\/sections/', $event['page'])) {
				// DELETE
				foreach($queries as $query) {
					if (preg_match('/^DELETE FROM sym_sections WHERE/', $query['sql'])) {
						$description = "Section deleted";
					}
				}
				// CREATE or UPDATE
				if (!$description) {
					preg_match("/'([a-zA-Z ]+)'/", $queries[0]['sql'], $matches);
					if (preg_match('/new\/$/', $event['page'])) {
						$description = "Section '{$matches[1]}' created";
					} else {
						$description = "Section '{$matches[1]}' updated";
					}
				}
			}
			
			elseif (preg_match('/blueprints\/pages/', $event['page'])) {
				// DELETE
				foreach($queries as $query) {
					if (preg_match('/^DELETE FROM sym_pages WHERE/', $query['sql'])) {
						$description = "Page deleted";
					}
				}
				// CREATE or UPDATE
				if (!$description) {
					preg_match("/'([a-zA-Z ]+)'/", $queries[0]['sql'], $matches);
					if (preg_match('/new\/$/', $event['page'])) {
						$description = "Section '{$matches[1]}' created";
					} else {
						$description = "Section '{$matches[1]}' updated";
					}
				}	
				
			}
			
			if ($description) {
				echo "-- " . $description . "\n";
			} else {
				echo "-- " . $event['page'] . "\n";
			}
			
			echo "\n";
			
			foreach($queries as $query) {
				// one last double check to make sure no content edits get through
				if (stristr($query['sql'], 'sym_entries_data_') && strtolower(substr($query['sql'], 0, 6)) == 'insert') continue;
				if (stristr($query['sql'], 'sym_entries_data_') && strtolower(substr($query['sql'], 0, 6)) == 'update') continue;
				echo($query['sql'] . ";\n");
			}
			
			echo "\n\n";
			
			$count++;
		}
	
	}
	
	public function formatQueryCount($count) {
		return $count . ' quer' . (($count == 1) ? 'y' : 'ies');
	}
	
	public function countQueries() {
		return $this->formatQueryCount(Administration::instance()->Database->fetchVar('count', 0, 'SELECT COUNT(id) as `count` FROM `db_sync`'));
	}
	
	public function formatEventCount($count) {
		return $count . ' event' . (($count == 1) ? '' : 's');
	}
	
	public function countEvents() {
		return $this->formatEventCount(Administration::instance()->Database->fetchVar('count', 0, 'SELECT COUNT(id) as `count` FROM `db_sync_events`'));
	}
	
	public function flush() {
		Administration::instance()->Database->query("DELETE FROM `db_sync` WHERE 1");
	}

}