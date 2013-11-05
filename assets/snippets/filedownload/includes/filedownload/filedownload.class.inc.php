<?php
/* * ********************************
  FileDownload Class (v2.7)
  Created By: Kyle Jaebker
  Modified By:
  Adam Strzelecki - 11/18/2006
  Adam Strzelecki - 01/02/2007
  Kyle Jaebker - 01/02/2007
  Kyle Jaebker - 09/08/2007
 * ******************************** */

if (!class_exists('FdDataDb')) {
	include MODX_BASE_PATH . 'assets/snippets/filedownload/includes/filedownload/data.db.class.inc.php';
}
if (!class_exists('evoChunkie')) {
	include MODX_BASE_PATH . 'assets/snippets/filedownload/includes/chunkie/chunkie.class.inc.php';
}

class FileDownload {

	var $_parameters = array();
	var $_templates = array();
	var $_config = array();
	var $_lang = array();
	var $allFiles = array();
	var $fcounts = array();
	var $urlSetting;
	var $db;

	function FileDownload() {
		$this->db = new FdDataDb;
	}

	function Run() {
		global $modx;
		$output = '';

		// Language string settings
		$this->_lang['delSuccess'] = !is_null($this->Get('delSuccess')) ? $this->Get('delSuccess') : 'You have deleted: ';
		$this->_lang['delError'] = !is_null($this->Get('delError')) ? $this->Get('delError') : 'There was an error deleting: ';
		$this->_lang['dirOpenError'] = !is_null($this->Get('dirOpenError')) ? $this->Get('dirOpenError') : 'Cannot open the directory: ';
		$this->_lang['notaDir'] = !is_null($this->Get('notaDir')) ? $this->Get('notaDir') : 'The path specified is not a directory: ';
		$this->_lang['noDownload'] = !is_null($this->Get('noDownload')) ? $this->Get('noDownload') : 'You do not have permission to download this file.';

		// Check for first run if DB count
		$this->_config['useDbCount'] = !is_null($this->Get('useDbCount')) ? $this->Get('useDbCount') : 1;
		$this->_config['skipTableCheck'] = !is_null($this->Get('skipTableCheck')) ? $this->Get('skipTableCheck') : 0;
		if ($this->_config['useDbCount'] && !$this->_config['skipTableCheck']) {
			$this->db->firstRun($this->db->config['fdTable']);
		}

		// Set date format
		$this->_config['dateFormat'] = !is_null($this->Get('dateFormat')) ? $this->Get('dateFormat') : 'Y-m-d';

		// Check for FileDownloadPlugin
		$this->_config['usePlugin'] = !is_null($this->Get('usePlugin')) ? $this->Get('usePlugin') : 0;

		// Get url config settings
		$this->_config['urlsetting'] = $modx->config['friendly_urls'] ? '?' : '&';
		$this->_config['curUrl'] = $modx->makeURL($modx->documentIdentifier);

		// Style Settings
		$this->_config['css']['alt'] = !is_null($this->Get('altCss')) ? $this->Get('altCss') : 'fd-alt';
		$this->_config['css']['firstFolder'] = !is_null($this->Get('firstFolderCss')) ? $this->Get('firstFolderCss') : '';
		$this->_config['css']['lastFolder'] = !is_null($this->Get('lastFolderCss')) ? $this->Get('lastFolderCss') : '';
		$this->_config['css']['firstFile'] = !is_null($this->Get('firstFileCss')) ? $this->Get('firstFileCss') : '';
		$this->_config['css']['lastFile'] = !is_null($this->Get('lastFileCss')) ? $this->Get('lastFileCss') : '';
		$this->_config['css']['folder'] = !is_null($this->Get('folderCss')) ? $this->Get('folderCss') : 'fd-folder';
		$this->_config['css']['file'] = !is_null($this->Get('fileCss')) ? $this->Get('fileCss') : 'fd-file';
		$this->_config['css']['parent'] = !is_null($this->Get('parentCss')) ? $this->Get('parentCss') : 'fd-parent';
		$this->_config['css']['directory'] = !is_null($this->Get('directoryCss')) ? $this->Get('directoryCss') : 'fd-directory';
		$this->_config['css']['path'] = !is_null($this->Get('pathCss')) ? $this->Get('pathCss') : 'fd-path';
		$this->_config['css']['extension'] = !is_null($this->Get('extCss')) ? $this->Get('extCss') : 0;

		// Image Settings
		$this->_config['imgLocat'] = !is_null($this->Get('imgLocat')) ? $this->Get('imgLocat') : '';
		if (!is_null($this->Get('imgTypes'))) {
			$imageChunk = $modx->getChunk($this->Get('imgTypes'));
			if ($imageChunk) {
				$imageChunk = explode(',', $imageChunk);
				$fdImages = array();
				foreach ($imageChunk as $v) {
					$tempImage = explode('=', trim($v));
					$fdImages[$tempImage[0]] = $tempImage[1];
				}
				$this->_config['imgTypes'] = $fdImages;
			} else {
				$this->_config['imgTypes'] = 0;
			}
		}

		// Check if webuser based foldername should be used
		$this->_config['userFolder'] = !is_null($this->Get('userFolder')) ? $this->Get('userFolder') : 0;

		// Check if multiple folders specified
		if (strpos($this->Get('getFolder'), ',')) {
			$this->_config['getFolder'] = explode(',', $this->Get('getFolder'));
			if ($this->_config['userFolder']) {
				foreach ($this->_config['getFolder'] as $key => $value) {
					$this->_config['getFolder'][$key] = $value . '/' . $modx->getLoginUserName();
				}
			}
			$this->_config['multiFolders'] = 1;
		} else {
			$this->_config['getFolder'] = $this->Get('getFolder');
			if ($this->_config['userFolder']) {
				$this->_config['getFolder'] .= '/' . $modx->getLoginUserName();
			}
			$this->_config['multiFolders'] = 0;
		}

		// Check if file description chunks specified
		if (!is_null($this->Get('chkDesc'))) {
			$descriptChunk = $modx->getChunk($this->Get('chkDesc'));
			if ($descriptChunk) {
				$this->_config['chkDesc'] = $descriptChunk;
			} else {
				$this->_config['chkDesc'] = 0;
			}
		}

		// If single file downloading is specified
		$this->_config['getFile'] = !is_null($this->Get('getFile')) ? $this->Get('getFile') : 0;

		// File extension filter settings
		$this->_config['showExt'] = is_null($this->Get('showExt')) ? 0 : explode(',', $this->Get('showExt'));
		$this->_config['hideExt'] = is_null($this->Get('hideExt')) ? 0 : explode(',', $this->Get('hideExt'));
		$this->_config['filterExt'] = ($this->_config['showExt'] || $this->_config['hideExt']) ? 1 : 0;

		// Get Permissions
		$this->_config['canDownload'] = !is_null($this->Get('downloadGroups')) ? $modx->isMemberOfWebGroup(explode(',', $this->Get('downloadGroups'))) : 1;
		$this->_config['canDelete'] = !is_null($this->Get('deleteGroups')) ? $modx->isMemberOfWebGroup(explode(',', $this->Get('deleteGroups'))) : 0;

		// Setup Directory Browsing
		$this->_config['browseDirectories'] = !is_null($this->Get('browseDirectories')) && !$this->_config['multiFolders'] ? 1 : 0;
		if ($this->_config['browseDirectories']) {
			// Just for safety, directory traversal should not be possible
			$badPath = array('../', '/../', '/..');
			$relPath = str_replace($badPath, '', $_GET['relPath']);
			$this->_config['getFolder'] .= $relPath ? '/' . $relPath : '';
			$this->_config['curRelPath'] = $relPath;
		}

		// Location of files for download count & download processing
		$this->_config['fileCount'] = 'assets/snippets/filedownload/filecount.txt';

		// Setup download counting
		$this->_config['countDownloads'] = !is_null($this->Get('countDownloads')) ? $this->Get('countDownloads') : 1;
		if ($this->_config['countDownloads'] && $this->_config['usePlugin']) {
			$this->loadCounts();
			$this->_config['countDownloads'] = 1;
		} else {
			$this->_config['downloadCount'] = 0;
			$this->_config['countDownloads'] = 0;
		}

		// Get the files into an array
		if ($this->_config['multiFolders']) {
			foreach ($this->_config['getFolder'] as $nFolder => $aFolder) {
				if (is_array($this->getFiles($aFolder, $nFolder))) {
					$this->allFiles = array_merge($this->allFiles, $this->getFiles($aFolder, $nFolder));
				}
			}
		} else {
			$this->allFiles = array_merge($this->allFiles, $this->getFiles($this->_config['getFolder']));
		}

		// Delete file if delete link clicked
		if ($this->_config['canDelete'] && isset($_GET['act'])) {
			$output .= $this->deleteFile();
		}

		// Sorting the array of files
		if ($this->_config['browseDirectories']) {
			$this->_config['userSort'] = 'type,';
			$this->_config['userSort'] .=!is_null($this->Get('userSort')) ? $this->Get('userSort') : 'filename';
			$this->_config['sortOrder'] = 'asc';
		} else {
			$this->_config['groupByDirectory'] = !is_null($this->Get('groupByDirectory')) ? $this->Get('groupByDirectory') : 0;
			$this->_config['userSort'] = $this->_config['groupByDirectory'] ? 'path,' : '';
			$this->_config['userSort'] .=!is_null($this->Get('userSort')) ? $this->Get('userSort') : 'filename';
			$this->_config['sortOrder'] = !is_null($this->Get('sortOrder')) ? $this->Get('sortOrder') : 'asc';
		}
		$this->customSort($this->allFiles, $this->_config['userSort'], $this->_config['sortOrder']);

		// Get the Templates
		$this->_config['splitter'] = !is_null($this->Get('splitter')) ? $this->Get('splitter') : '<!-- Fd:Splitter -->';

		if ($this->Get('tplList'))
			$this->_templates['orig'] = $modx->getChunk($this->Get('tplList'));
		if (empty($this->_templates['orig']))
			$this->_templates['orig'] = $this->getDefaultLayout();

		$this->_templates['parts'] = $this->getTemplateArray($this->_templates['orig'], $this->_config['splitter']);

		// Create the output
		$output .= $this->generateOutput();
		return $output;
	}

	function Get($field) {
		return $this->_parameters[$field];
	}

	function Set($field, $value) {
		$this->_parameters[$field] = $value;
	}

	function generateOutput() {
		$alt = 0;
		$iteration = 0;

		$parser = new evoChunkie('@CODE ' . $this->_templates['parts']['header']);
		// Add the path for browse directories & up level link
		if ($this->_config['browseDirectories']) {
			$placeholder = array(
				'path' => $this->_config['curRelPath'] ? '/' . $this->_config['curRelPath'] : '/',
				'class' => $this->setClass('path', $alt)
			);
			$parser->CreateVars($placeholder, 'fd');
			$output = $parser->Render();
			$alt = $alt ? 0 : 1;
			$iteration++;

			if ($this->_config['curRelPath']) {
				$parser = new evoChunkie('@CODE ' . $this->_templates['parts']['parent']);
				$relPathParts = explode('/', $this->_config['curRelPath']);
				array_pop($relPathParts);
				$placeholder = array(
					'filename' => '../',
					'extension' => '',
					'path' => '',
					'size' => '',
					'sizetext' => '-',
					'type' => 'dir',
					'date' => '',
					'description' => '',
					'image' => $this->_config['imgTypes'] ? $this->selectImage('parent') : '',
					'delete' => '',
					'count' => '',
					'link' => $this->createLink(1, '', implode('/', $relPathParts), 1),
					'class' => $this->setClass('parent', $alt),
					'iteration' => $iteration
				);
				$parser->CreateVars($placeholder, 'fd');
				$rowOutput = $parser->Render();
				$output .= $rowOutput;
				$alt = $alt ? 0 : 1;
				$iteration++;
			}
		} else {
			$output = $parser->Render();
		}

		$alt = $alt ? 0 : 1;
		$iteration++;
		$prevDir = '';
		$fileTotal = 0;
		$dirTotal = 0;

		foreach ($this->allFiles as $v) {
			if ($this->_config['groupByDirectory'] && $v['path'] !== $prevDir) {
				$parser = new evoChunkie('@CODE ' . $this->_templates['parts']['directory']);
				$placeholder = array(
					'directory' => $v['path'],
					'class' => $this->setClass('multidir', $alt),
					'iteration' => $iteration
				);
				$parser->CreateVars($placeholder, 'fd');
				$dirOutput = $parser->Render();
				$output .= $dirOutput;
				$alt = $alt ? 0 : 1;
			}
			if ($v['path'] !== $prevDir) {
				$fileTotal = $this->fcounts[$v['path']]['files'];
				$dirTotal = $this->fcounts[$v['path']]['dirs'];
				$dirCount = 1;
				$fileCount = 1;
			}
			if ($v['type'] == 'dir') {
				$parser = new evoChunkie('@CODE ' . $this->_templates['parts']['folder']);
				$first = ($dirCount == 1) ? 1 : 0;
				$last = ($dirCount == $dirTotal) ? 1 : 0;
				$dirCount++;
			} else {
				$parser = new evoChunkie('@CODE ' . $this->_templates['parts']['row']);
				$first = ($fileCount == 1) ? 1 : 0;
				$last = ($fileCount == $fileTotal) ? 1 : 0;
				$fileCount++;
			}
			if ($this->_config['canDelete']) {
				$delRelPath = $this->_config['curRelPath'] ? ('&relPath=' . $this->_config['curRelPath']) : '';
				$deleteLink = $this->_config['curUrl'] . $this->_config['urlsetting'] . 'act=' . $v['delete'] . $delRelPath;
				$v['delete'] = $this->_templates['parts']['delete'];
				$v['delete'] = str_replace('[+fd.deletelink+]', $deleteLink, $v['delete']);
			} else {
				$v['delete'] = '';
			}
			$placeholder = $v;
			$placeholder['class'] = $this->setClass($v['type'], $alt, $first, $last, $v['extension']);
			$placeholder['filenumber'] = ($v['type'] == 'dir') ? $dirCount - 1 : $fileCount - 1;
			$placeholder['iteration'] = $iteration;
			$parser->CreateVars($placeholder, 'fd');
			$rowOutput = $parser->Render();
			$output .= $rowOutput;

			$prevDir = $v['path'];
			$alt = $alt ? 0 : 1;
			$iteration++;
		}
		$parser = new evoChunkie('@CODE ' . $this->_templates['parts']['footer']);
		$placeholder = array(
			'iteration' => $iteration
		);
		$parser->CreateVars($placeholder, 'fd');
		$output .= $parser->Render();
		return $output;
	}

	function validExtension($extension) {
		if ($this->_config['showExt']) {
			$validExt = FALSE;
			foreach ($this->_config['showExt'] as $showExt) {
				if (!(strpos($showExt, $extension) === FALSE)) {
					$validExt = TRUE;
					break;
				}
			}
		} else if ($this->_config['hideExt']) {
			$validExt = TRUE;
			foreach ($this->_config['hideExt'] as $hideExt) {
				if (!(strpos($hideExt, $extension) === FALSE)) {
					$validExt = FALSE;
					break;
				}
			}
		}
		if ($validExt === FALSE) {
			return 0;
		} else {
			return 1;
		}
	}

	function fileSizeText($fileSize) {
		if ($fileSize == 0)
			$returnVal = '0 bytes';
		else if ($fileSize > 1024 * 1024 * 1024)
			$returnVal = (ceil($fileSize / (1024 * 1024 * 1024) * 100) / 100) . ' GB';
		else if ($fileSize > 1024 * 1024)
			$returnVal = (ceil($fileSize / (1024 * 1024) * 100) / 100) . ' MB';
		else if ($fileSize > 1024)
			$returnVal = (ceil($fileSize / 1024 * 100) / 100) . ' kB';
		else
			$returnVal = $fileSize . ' B';
		return $returnVal;
	}

	function selectImage($extension) {
		if (array_key_exists($extension, $this->_config['imgTypes'])) {
			$image = $this->_config['imgTypes'][$extension];
		} else {
			$image = $this->_config['imgTypes']['default'];
		}
		return $this->_config['imgLocat'] . '/' . $image;
	}

	function getDescription($filename) {
		$filename = str_replace('/', '\/', $filename);
		$filename = str_replace('.', '\.', $filename);
		$pattern = '/' . $filename . '\|(.*?)' . '\|{2}/';
		preg_match($pattern, $this->_config['chkDesc'], $result);
		return $result[1];
	}

	function getDownloadCount($filename) {
		if (array_key_exists($filename, $this->_config['downloadCount'])) {
			$dwnCount = $this->_config['downloadCount'][$filename];
		} else {
			$dwnCount = 0;
		}
		return $dwnCount;
	}

	function createLink($isDir, $filelocat, $filename, $isParent = 0, $fileSize = 0, $multiFolderNum = 0) {
		if ($isDir) {
			$fileLink = $this->_config['curUrl'] . $this->_config['urlsetting'] . 'relPath=';
			if ($isParent) {
				if ($filename) {
					$fileLink .= $filename;
				} else {
					$fileLink = $this->_config['curUrl'];
				}
			} else {
				$fileLink .= $this->_config['curRelPath'] ? $this->_config['curRelPath'] . '/' . $filename : $filename;
			}
		} else {
			if ($this->_config['canDownload']) {
				if ($this->_config['usePlugin']) {
					if ($this->_config['multiFolders']) {
						$multiFolderLink = '&dir=' . $multiFolderNum;
					} else {
						$multiFolderLink = '';
					}
					if (strlen($this->_config['curRelPath'])) {
						$filelocat = $this->_config['curRelPath'] . '/' . $filename;
					} else {
						$filelocat = $filename;
					}
					if ($this->_config['urlsetting'] == '?') {
						$fileLink = str_replace('.html', '', $this->_config['curUrl']) . '/' . $filelocat . $multiFolderLink;
					} else {
						$fileLink = $this->_config['curUrl'] . '&d=' . $filelocat . $multiFolderLink;
					}
				} else {
					$fileLink = $filelocat;
				}
			} else {
				$fileLink = 'javascript:alert(\'' . $this->_lang['noDownload'] . '\')';
			}
		}
		return $fileLink;
	}

	function getFiles($path, $multiFolderNum = 0) {
		$files = array();

		if (is_dir($path)) {
			if ($dh = opendir($path)) {
				$i = 0;
				$countFiles = 0;
				$countDirs = 0;

				while (($file = readdir($dh)) !== false) {
					if ($file == '.' || $file == '..' || $file == '.htaccess' || $file == '.htpasswd')
						continue;

					if ($this->_config['getFile']) {
						if ($file !== $this->_config['getFile']) {
							continue;
						}
					}

					$fullpath = $path . '/' . $file;

					// Check extension if valid extensions are specified & remove folders if no Browse Directory
					$extension = strtolower(substr($file, strrpos($file, '.') + 1));
					$fileType = filetype($fullpath);
					$isDir = ($fileType == 'dir') ? 1 : 0;

					if ($this->_config['browseDirectories']) {
						if ($isDir) {
							$validFile = 1;
						} else {
							$validFile = $this->_config['filterExt'] ? $this->validExtension($extension) : 1;
						}
					} else {
						if ($isDir) {
							$validFile = 0;
						} else {
							$validFile = $this->_config['filterExt'] ? $this->validExtension($extension) : 1;
						}
					}

					if ($validFile) {
						$fileStats = stat($fullpath);
						$files[$i]['filename'] = $file;
						$files[$i]['extension'] = $isDir ? '' : $extension;
						$files[$i]['path'] = $path;
						$files[$i]['size'] = $isDir ? '' : $fileStats['size'];
						$files[$i]['sizetext'] = $isDir ? '-' : $this->fileSizeText($fileStats['size']);
						$files[$i]['type'] = $fileType;
						$files[$i]['date'] = date($this->_config['dateFormat'], $fileStats['mtime']);
						$files[$i]['unixdate'] = $fileStats['mtime'];
						$files[$i]['description'] = $this->_config['chkDesc'] ? $this->getDescription($fullpath) : '';
						if ($this->_config['imgTypes']) {
							$files[$i]['image'] = $isDir ? $this->selectImage('folder') : $this->selectImage($extension);
						} else {
							$files[$i]['image'] = '';
						}
						$files[$i]['delete'] = md5('act:del||filename:' . $fullpath);
						$files[$i]['count'] = $isDir ? '' : $this->_config['countDownloads'] ? $this->getDownloadCount($fullpath) : 0;
						$files[$i]['link'] = $this->createLink($isDir, $fullpath, $file, 0, $fileStats['size'], $multiFolderNum);
						if ($fileType == 'dir') {
							$countDirs++;
						} else {
							$countFiles++;
						}
					}
					if ($this->_config['getFile']) {
						if ($file == $this->_config['getFile']) {
							break;
						}
					}
					$i++;
				}
				closedir($dh);
			}
			else
				$files = $this->_lang['dirOpenError'] . $path;
		}
		else
			$files = $this->_lang['notaDir'] . $path;
		$this->fcounts[$path]['dirs'] = $countDirs;
		$this->fcounts[$path]['files'] = $countFiles;
		return $files;
	}

	function getDefaultLayout() {
		return file_get_contents(MODX_BASE_PATH . 'assets/snippets/filedownload/templates/filedownload.template.html');
	}

	function getTemplateArray($html, $tplSeparator) {
		list($tpl['header'], $tpl['parent'], $tpl['folder'], $tpl['row'], $tpl['delete'], $tpl['directory'], $tpl['footer']) = explode($tplSeparator, $html);
		return $tpl;
	}

	function customSort(&$finalArray, $fields, $order) {
		// Covert $fields string to array
		foreach (explode(',', $fields) as $s)
			$sortfields[] = trim($s);

		$code = "";

		for ($c = 0; $c < count($sortfields); $c++) {
			$code .= "\$retval = strnatcmp(strtolower(\$a['$sortfields[$c]']), strtolower(\$b['$sortfields[$c]'])); if(\$retval) return \$retval; ";
		}

		$code .= "return \$retval;";

		$params = ($order == 'asc') ? '$a,$b' : '$b,$a';
		usort($finalArray, create_function($params, $code));
	}

	function loadCounts() {
		$loadWhere = ($this->_config['useDbCount']) ? 'db' : 'file';
		$this->_config['downloadCount'] = $this->db->loadCounts($this->db->config['fdTable'], $loadWhere, $this->_config['fileCount']);
	}

	function setClass($type, $alt = 0, $first = 0, $last = 0, $ext = '') {
		$class = '';

		switch ($type) {
			case 'dir':
				if ($this->_config['css']['folder'])
					$class .= $this->_config['css']['folder'] . ' ';
				if ($this->_config['css']['alt'] && $alt)
					$class .= $this->_config['css']['alt'] . ' ';
				if ($this->_config['css']['firstFolder'] && $first)
					$class .= $this->_config['css']['firstFolder'] . ' ';
				if ($this->_config['css']['lastFolder'] && $last)
					$class .= $this->_config['css']['lastFolder'] . ' ';
				break;
			case 'file':
				if ($this->_config['css']['file'])
					$class .= $this->_config['css']['file'] . ' ';
				if ($this->_config['css']['alt'] && $alt)
					$class .= $this->_config['css']['alt'] . ' ';
				if ($this->_config['css']['firstFile'] && $first)
					$class .= $this->_config['css']['firstFile'] . ' ';
				if ($this->_config['css']['lastFile'] && $last)
					$class .= $this->_config['css']['lastFile'] . ' ';
				if ($this->_config['css']['extension'])
					$class .= 'fd-' . $ext . ' ';
				break;
			case 'path':
				if ($this->_config['css']['path'])
					$class .= $this->_config['css']['path'] . ' ';
				if ($this->_config['css']['alt'] && $alt)
					$class .= $this->_config['css']['alt'] . ' ';
				break;
			case 'multidir':
				if ($this->_config['css']['directory'])
					$class .= $this->_config['css']['directory'] . ' ';
				if ($this->_config['css']['alt'] && $alt)
					$class .= $this->_config['css']['alt'] . ' ';
				break;
			case 'parent':
				if ($this->_config['css']['parent'])
					$class .= $this->_config['css']['parent'] . ' ';
				if ($this->_config['css']['alt'] && $alt)
					$class .= $this->_config['css']['alt'] . ' ';
				break;
		}

		$class = $class ? ' class="' . trim($class) . '"' : '';

		return $class;
	}

	function deleteFile() {
		foreach ($this->allFiles as $n => $v) {
			$delParam = $_GET['act'];
			if ($delParam == $v['delete']) {
				$dfilename = $v['path'] . '/' . $v['filename'];
				$delReturn = @ unlink($dfilename);
				if ($delReturn) {
					unset($this->allFiles[$n]);
					return '<span class="fd-delete-msg">' . $this->_lang['delSuccess'] . $v['filename'] . '</span>';
				} else {
					return '<span class="fd-delete-msg">' . $this->_lang['delError'] . $v['filename'] . '</span>';
				}
			}
		}
		return '<span class="fd-delete-msg">You can\'t delete that file!</span>';
	}

}

?>
