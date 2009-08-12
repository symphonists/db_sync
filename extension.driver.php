<?php

	require_once(EXTENSIONS . '/asdc/lib/class.asdc.php');
	
	Class extension_db_sync extends Extension {
	
		public function about() {
			return array(
				'name'			=> 'Database Synchroniser',
				'version'		=> '0.3',
				'release-date'	=> '2009-08-12',
				'author'		=> array(
					'name'			=> 'Nick Dunn, Richard Warrender',
					'website'		=> 'http://airlock.com',
					'email'			=> 'nick.dunn@airlock.com'
				),
				'description'	=> 'Logs structural database changes to allow syncing between builds.'
			);
		}
		
		/*public function fetchNavigation() {
			return array(
				array(
					'location' => 'System',
					'name'	=> 'Database Sync',
					'link'	=> '/log/',
				)
			);
		}*/
		
		public function uninstall() {
			$this->_Parent->Database->query("DROP TABLE `db_sync`");
		}
		
		public function install() {
		
			$this->_Parent->Database->query("
				CREATE TABLE `db_sync` (
				  `id` int(11) NOT NULL auto_increment,
				  `sql` text,
				  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
				  PRIMARY KEY  (`id`)
				)");
				
			return true;
		}
		
		public function getSubscribedDelegates(){
			return array(
						array(
							'page' => '/system/preferences/',
							'delegate' => 'AddCustomPreferenceFieldsets',
							'callback' => 'appendPreferences'
						),

					);
		}
		
		protected function __downloadSQL() {
			require_once(dirname(__FILE__) . '/lib/class.logviewer.php');
			
			$log = new LogViewer();
			$log->display(LogViewer::MODE_DOWNLOAD);
		}
		
		protected function __flushLog() {
			require_once(dirname(__FILE__) . '/lib/class.logviewer.php');
			
			$log = new LogViewer();
			$log->flush();
		}
		
		public function appendAssets($context){
			echo "Appending assets";
			exit;
		}
		
		public function appendPreferences($context){
						
			$context['parent']->Page->addScriptToHead(URL . '/extensions/db_sync/assets/ui-logic.js', 666);
			
			if(isset($_POST['action']['db_sync_flush'])){
				$this->__flushLog($context);
			}
			
			if(isset($_POST['action']['db_sync_download'])) {
				$this->__downloadSQL();
			}
			
			$group = new XMLElement('fieldset');
			$group->setAttribute('class', 'settings');
			
			// Sync flush
			$group->appendChild(new XMLElement('legend', 'DB Sync'));			
			$div = new XMLElement('div', NULL, array('id' => 'file-actions', 'class' => 'label'));			
			$span = new XMLElement('span');

			if(!extension_loaded('zlib')) {
				$span->appendChild(new XMLElement('p', '<strong>Warning: It appears you do not have the zlib extension available.'));
			}
			else{
				$span->appendChild(new XMLElement('button', 'Download SQL', array('name' => 'action[db_sync_download]', 'type' => 'submit')));
				$span->appendChild(new XMLElement('button', 'Flush Log', array('name' => 'action[db_sync_flush]', 'type' => 'submit')));
			}
			$div->appendChild($span);
			$div->appendChild(new XMLElement('p', 'Deletes current queries in the db sync log.', array('class' => 'help')));	
			$group->appendChild($div);
			
			$context['wrapper']->appendChild($group);
						
		}
		

	}
	
?>