<?php
/* * ********************************
 * File Download Plugin (v1.3)
 * Created By: Adam Strzelecki
 * Last Modified:
 * Adam Strzelecki - 03/01/2007
 * Kyle Jaebker - 01/02/2007
 * Thomas Jakobi - 21/05/2013
 *
 * Plugin Configuration:
 * &countDownloads=Count Downloads;list;yes,no;yes &useDbCount=Store Count Where;list;db,file;db
 *
 * Extended Plugin Configuration:
 * &countDownloads=Count Downloads;list;yes,no;yes &useDbCount=Store Count Where;list;db,file;db &blockSize=Block Size;text;262144 &fileDownloadFolderTV=File Download Folder Template Variable;text;FileDownloadFolder &userFolder=Webuser based foldername;list;yes,no;no
 *
 * System Event:
 * OnWebPageInit
 *
 * Info: For use with the FileDownload Snippet v2.7
 * ******************************** */

if (MODX_BASE_PATH == '') {
	die('<h1>ERROR:</h1><p>Please use do not access this file directly.</p>');
}
if (!class_exists('FdDataDb')) {
	include MODX_BASE_PATH . 'assets/snippets/filedownload/includes/filedownload/data.db.class.inc.php';
}

$countDownloads = (isset($countDownloads) && $countDownloads != 'yes') ? 'no' : 'yes';
$useDbCount = (isset($useDbCount) && $useDbCount != 'db') ? 'file' : 'db';
$blockSize = (isset($blockSize)) ? intval($blockSize) : 256 * 1024;
$fileDownloadFolderTV = (isset($fileDownloadFolderTV)) ? $fileDownloadFolderTV : 'FileDownloadFolder';
$userFolder = (isset($userFolder) && $userFolder != 'no') ? 'yes' : 'no';

$e = &$modx->event;
if (isset($_GET['d']) && trim($_GET['d']) != '') {
	if (preg_match('/(^|[\/])\.+([\/]|$)/', trim($_GET['d']))) {
		// don't allow any /. or ./ combinations in $_GET['d']
		return;
	}
	$downloadFolder = $modx->getTemplateVarOutput($fileDownloadFolderTV);
	if (!is_array($downloadFolder)) {
		// template variable $fileDownloadFolderTV does not exist
		return;
	}
	$downloadFolder = $downloadFolder[$fileDownloadFolderTV];
	if (!is_string($downloadFolder)) {
		if (!(is_array($downloadFolder) && is_string($downloadFolder[1]))) {
			// template variable $fileDownloadFolderTV is set wrong
			return;
		} else {
			$downloadFolder = $downloadFolder[1];
		}
	}
	if ($downloadFolder == '') {
		// template variable $fileDownloadFolderTV is empty
		return;
	}

	// Multiple folders case (comma separated)
	if (strpos($downloadFolder, ',') && isset($_GET['dir'])) {
		$possiblePaths = explode(',', $downloadFolder);
		$downloadFolder = $possiblePaths[intval($_GET['dir'])];
	}

	if ($userFolder == 'yes') {
		$downloadFolder .= '/' . $modx->getLoginUserName();
	}

	$countFile = $downloadFolder . '/' . trim($_GET['d']);
	$downloadFile = realpath($downloadFolder . '/' . trim($_GET['d']));
	if (is_file($downloadFile)) {
		// Count download
		if ($countDownloads == 'yes') {
			$updateWhere = $useDbCount;
			$fdData = new FdDataDb;
			$fdData->updateCount($countFile, $updateWhere);
		}
		// Last modified match
		$downloadTime = gmdate("D, d M Y H:i:s", filemtime($downloadFile)) . " GMT";
		$downloadSize = filesize($downloadFile);
		$downloadETag = md5("$downloadFile||$downloadTime||$downloadSize");
		if ($_SERVER["HTTP_IF_NONE_MATCH"] == $downloadETag || $_SERVER["HTTP_IF_MODIFIED_SINCE"] == $downloadTime) {
			header("HTTP/1.1 304 Not Modified");
			header("Last-Modified: $downloadTime");
			header("ETag: $downloadETag");
			exit;
		}
		// Put headers
		$downloadInfo = pathinfo($downloadFile);
		switch ($downloadInfo['extension']) {
			case 'asf': $type = 'video/x-ms-asf';
				break;
			case 'avi': $type = 'video/x-msvideo';
				break;
			case 'bin': $type = 'application/octet-stream';
				break;
			case 'bmp': $type = 'image/bmp';
				break;
			case 'cgi': $type = 'magnus-internal/cgi';
				break;
			case 'css': $type = 'text/css';
				break;
			case 'dcr': $type = 'application/x-director';
				break;
			case 'dxr': $type = 'application/x-director';
				break;
			case 'dll': $type = 'application/octet-stream';
				break;
			case 'doc': $type = 'application/msword';
				break;
			case 'exe': $type = 'application/octet-stream';
				break;
			case 'gif': $type = 'image/gif';
				break;
			case 'gtar': $type = 'application/x-gtar';
				break;
			case 'gz': $type = 'application/gzip';
				break;
			case 'htm': $type = 'text/html';
				break;
			case 'html': $type = 'text/html';
				break;
			case 'iso': $type = 'application/octet-stream';
				break;
			case 'jar': $type = 'application/java-archive';
				break;
			case 'java': $type = 'text/x-java-source';
				break;
			case 'jnlp': $type = 'application/x-java-jnlp-file';
				break;
			case 'js': $type = 'application/x-javascript';
				break;
			case 'jpg': $type = 'image/jpg';
				break;
			case 'jpe': $type = 'image/jpg';
				break;
			case 'jpeg': $type = 'image/jpg';
				break;
			case 'lzh': $type = 'application/octet-stream';
				break;
			case 'mdb': $type = 'application/mdb';
				break;
			case 'mid': $type = 'audio/x-midi';
				break;
			case 'midi': $type = 'audio/x-midi';
				break;
			case 'mov': $type = 'video/quicktime';
				break;
			case 'mp2': $type = 'audio/x-mpeg';
				break;
			case 'mp3': $type = 'audio/mpeg';
				break;
			case 'mpg': $type = 'video/mpeg';
				break;
			case 'mpe': $type = 'video/mpeg';
				break;
			case 'mpeg': $type = 'video/mpeg';
				break;
			case 'pdf': $type = 'application/pdf';
				break;
			case 'php': $type = 'application/x-httpd-php';
				break;
			case 'php3': $type = 'application/x-httpd-php3';
				break;
			case 'php4': $type = 'application/x-httpd-php';
				break;
			case 'png': $type = 'image/png';
				break;
			case 'ppt': $type = 'application/mspowerpoint';
				break;
			case 'qt': $type = 'video/quicktime';
				break;
			case 'qti': $type = 'image/x-quicktime';
				break;
			case 'rar': $type = 'encoding/x-compress';
				break;
			case 'ra': $type = 'audio/x-pn-realaudio';
				break;
			case 'rm': $type = 'audio/x-pn-realaudio';
				break;
			case 'ram': $type = 'audio/x-pn-realaudio';
				break;
			case 'rtf': $type = 'application/rtf';
				break;
			case 'swa': $type = 'application/x-director';
				break;
			case 'swf': $type = 'application/x-shockwave-flash';
				break;
			case 'tar': $type = 'application/x-tar';
				break;
			case 'tgz': $type = 'application/gzip';
				break;
			case 'tif': $type = 'image/tiff';
				break;
			case 'tiff': $type = 'image/tiff';
				break;
			case 'torrent': $type = 'application/x-bittorrent';
				break;
			case 'txt': $type = 'text/plain';
				break;
			case 'wav': $type = 'audio/wav';
				break;
			case 'wma': $type = 'audio/x-ms-wma';
				break;
			case 'wmv': $type = 'video/x-ms-wmv';
				break;
			case 'xls': $type = 'application/xls';
				break;
			case 'xml': $type = 'application/xml';
				break;
			case '7z': $type = 'application/x-compress';
				break;
			case 'zip': $type = 'application/x-zip-compressed';
				break;
			default: $type = 'application/force-download';
				break;
		}
		// Partial content
		$starts = array();
		$ends = array();
		$parts = 0;
		// Loop trough all parts
		foreach (explode(',', $_SERVER['HTTP_RANGE']) as $brange) {
			if (preg_match("/([0-9]*)\-([0-9]*)$/", $brange, $matches)) {
				if ($matches[1] == '') {
					$starts[] = $downloadSize - intval($matches[2]);
					$ends[] = $downloadSize - 1;
				} else {
					$starts[] = intval($matches[1]);
					$ends[] = intval($matches[2]);
				}
				$parts++;
			}
		}
		if (!$parts) {
			$starts[] = 0;
			$ends[] = $downloadSize - 1;
		}
		if ($fp = @fopen($downloadFile, "r")) {
			while (@ob_end_clean());
			set_time_limit(0);
			ignore_user_abort(1);
			$aborted = false;
			for ($rno = 0; !$aborted && (($rno < $parts) || (!$rno && !$parts)); $rno++) {
				$start = $starts[$rno];
				$end = $ends[$rno];
				if (!$end || $end < $start) {
					$size = $downloadSize - $start;
					$end = $start + $size - 1;
				} else {
					$size = $end - $start + 1;
				}
				if (!$rno) {
					if ($parts) {
						header("HTTP/1.1 206 Partial Content");
					}
					if ($parts == 1) {
						header("Content-Range: bytes {$start}-{$end}/{$downloadSize}");
						fseek($fp, $start);
					}
					header("Accept-Ranges: bytes");
					header("Last-Modified: $downloadTime");
					header("Content-Disposition: inline; filename=\"" . str_replace('"', "'", $downloadInfo['basename']) . "\"");
					header("ETag: $downloadETag");
				}
				if (!$rno && $parts > 1) {
					header("Content-type: multipart/byteranges; boundary=THIS_STRING_SEPARATES");
				}
				if (!$parts) {
					header("Content-Type: $type");
					header("Content-Transfer-Encoding: binary");
					header("Content-Length: $size");
				}
				// This is the case we use multipart
				if ($parts > 1) {
					if ($rno) {
						echo "\r\n\r\n";
					}
					echo "--THIS_STRING_SEPARATES\r\n";
					echo "Content-Type: $type\r\n";
					echo "Content-Range: bytes {$start}-{$end}/{$downloadSize}\r\n\r\n";
					fseek($fp, $start);
				}
				while ($size && !($aborted = connection_aborted())) {
					$buff = ($size > $blockSize) ? $blockSize : $size;
					print(fread($fp, $buff));
					$size -= $buff;
				}
			}
			fclose($fp);
			exit;
		}
	}
}
?>