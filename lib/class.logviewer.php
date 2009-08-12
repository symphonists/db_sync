<?php

class LogViewer {

	const MODE_DOWNLOAD = "download";
	const MODE_ECHO = "echo";

	public function display($mode){

		ob_start();
	
		echo "--\n";
		echo "-- DB Sync from " . $_SERVER['HTTP_HOST'] . ' at ' . gmdate('D, d M Y H:i:s') . " GMT\n";
		echo "--\n\n";
				
		try {
			$this->__printDump();
		} catch(Exception $e) {
			ob_end_clean();
		
			print_r(ASDCLoader::instance()->lastError());
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
		$sql = sprintf("SELECT SQL_NO_CACHE e.id, e.* FROM `db_sync` AS `e` %s ORDER BY ID ASC",
			$extra_where
		);
		
		$queries = ASDCLoader::instance()->query($sql);

		foreach($queries as $query) {
			if (stristr($query->sql, 'sym_entries_data_') && strtolower(substr($query->sql, 0, 6)) == 'insert') continue;
			if (stristr($query->sql, 'sym_entries_data_') && strtolower(substr($query->sql, 0, 6)) == 'update') continue;
			echo($query->sql . ";\n");
		}
	
	}
	
	public function flush() {
		$queries = ASDCLoader::instance()->query("DELETE FROM `db_sync` WHERE 1");
	}

}