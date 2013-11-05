<?php

/**********************************
FileDownload DB Class (v2.6)
Created By: Kyle Jaebker
Last Modified: 01/02/2007
**********************************/

class FdDataDb {

	var $messageQuit;
	var $config = array();
	
	function FdDataDb($pre=NULL) {
		$this->config['table_prefix'] = ($pre !== NULL) ? $pre : $GLOBALS['table_prefix'];
		$this->config['fdTable'] = $this->config['table_prefix'] . 'fd_count';
	}
   
   function firstRun($dbTable) {
		global $modx;
		$fd = '%'.$this->config['fdTable'].'%';
		$rs = $modx->db->query("SHOW TABLES LIKE '".$fd."'");
		$count = $modx->db->getRecordCount($rs);
		
		if ($count==0) {
			$sql = 'CREATE TABLE `'.$dbTable.'` (`id` int(10) NOT NULL auto_increment,`filename` text default NULL,`count` int(10) default 0,  PRIMARY KEY  (`id`)) ENGINE=MyISAM';
			$modx->db->query($sql);	
		}
	}
	
	function loadCounts($dbTable,$loadWhere='db',$countFile='') {
		global $modx;
		$fdCounts = array();
		
		switch ($loadWhere) {
			case 'db':
				$sql = 'select * from `'.$dbTable.'`';
				$rs = $modx->db->query($sql);
				while ($row = $modx->db->getRow($rs)) {
					$fdCounts[$row['filename']] = $row['count'];
				}
				break;
			case 'file':
				if(!$handle = fopen($countFile,'r+')) {
					$fdCounts[] = 'file open failed: '.$this->_config['fileCount'];
				} else {
					while ((list($fname,$count) = fgetcsv($handle, 1000)) !== FALSE) {
						$fdCounts[$fname] = $count;
					}
					fclose($handle);
				}
				break;
		}
		return $fdCounts;
	}
	
	function updateCount($filename,$updateWhere='db') {
		global $modx;
		
		switch ($updateWhere) {
			case 'db':
				$sql = 'select * from ' . $this->config['fdTable'] . ' where ' . $this->config['fdTable'] . '.filename = \'' . $filename . '\'';
				
				if (!$rs = $modx->db->query($sql)) {
					$this->messageQuit = 'Error updating count.';
				} else {
					if ($modx->db->getRecordCount($rs) > 0) {
						$sql = 'update ' . $this->config['fdTable'] . ' set count=count+1 WHERE filename = \'' . $filename . '\'';
					} else {
						$sql = 'insert into ' . $this->config['fdTable'] . ' (filename,count) values (\'' . $filename . '\',1)'; 
					}
					$modx->db->query($sql);
				}
				break;
			case 'file':
				$countPath = 'assets/snippets/filedownload/filecount.txt';
				if(!$handle = fopen($countPath,'r+')) {
				    return 'file open failed: '.$countPath;
				} else {
				    $i = 0;
				    // Get the files already counted
				    while ((list($fname,$count) = fgetcsv($handle, 1000)) !== FALSE) {
				            $files[$i]['name'] = $fname;
				            $files[$i]['count']  = $count;
				            $i++;
				    }

				    $fileInTxt = 0;
				    // Check if current file has been counted
				    foreach ( array_keys($files) as $key ) {
				        $myFile =& $files[$key];
				        if ($myFile['name'] == $filename) {
				            $myFile['count']++;
				            $fileInTxt = 1;
				            break;
				        }
				    }
				    fclose($handle);
				    // If file has not been counted add it to the array
				    if (!$fileInTxt) {
				        $i++;
				        $files[$i]['name'] = $filename;
				        $files[$i]['count'] = 1;
				    }
				    // ReOutput the count file
				    $handle = fopen($countPath,'w');
				    foreach ( $files as $file ) {
				        $str = $file['name'] . ',' . $file['count'] . "\r\n";
				        fwrite($handle, $str);
				    }
				    fclose($handle);
				}
				break;
		}
	}
}
?>
