<?php
/* * ********************************
 * FileDownload (v2.7)
 * Created By: Kyle Jaebker
 * Modified By:
 * Adam Strzelecki - 11/18/2006
 * Adam Strzelecki - 01/02/2007
 * Kyle Jaebker - 01/02/2007
 * Kyle Jaebker - 09/08/2007
 * Thomas Jakobi - 21/05/2013
 * ******************************** */

if (MODX_BASE_PATH == '') {
	die('<h1>ERROR:</h1><p>Please use do not access this file directly.</p>');
}

//Include the FileDownload Class
if (!class_exists('FileDownload')) {
	include MODX_BASE_PATH . 'assets/snippets/filedownload/includes/filedownload/filedownload.class.inc.php';
}

//Check the File Download Plugin
$downloadFolder = $modx->getTemplateVar('FileDownloadFolder');
if (is_array($downloadFolder) && isset($downloadFolder['value']) && trim($downloadFolder['value']) != '') {
	$getFolder = trim($downloadFolder['value']);
	$usePlugin = 1;
} else if (!isset($getFolder) || trim($getFolder) == '') {
	return;
}

$fileDownload = new FileDownload;
$fileDownload->Set('getFolder', $getFolder);
$fileDownload->Set('usePlugin', $usePlugin);
$fileDownload->Set('getFile', $getFile);
$fileDownload->Set('tplList', $tplList);
$fileDownload->Set('splitter', $splitter);
$fileDownload->Set('chkDesc', $chkDesc);
$fileDownload->Set('userSort', $userSort);
$fileDownload->Set('sortOrder', $sortOrder);
$fileDownload->Set('showExt', $showExt);
$fileDownload->Set('hideExt', $hideExt);
$fileDownload->Set('downloadGroups', $downloadGroups);
$fileDownload->Set('deleteGroups', $deleteGroups);
$fileDownload->Set('countDownloads', $countDownloads);
$fileDownload->Set('fileCount', $fileCount);
$fileDownload->Set('useDbCount', $useDbCount);
$fileDownload->Set('skipTableCheck', $skipTableCheck);
$fileDownload->Set('download', $downloadFile);
$fileDownload->Set('imgLocat', $imgLocat);
$fileDownload->Set('imgTypes', $imgTypes);
$fileDownload->Set('browseDirectories', $browseDirectories);
$fileDownload->Set('groupByDirectory', $groupByDirectory);
$fileDownload->Set('dateFormat', $dateFormat);
$fileDownload->Set('altCss', $altCss);
$fileDownload->Set('firstFolderCss', $firstFolderCss);
$fileDownload->Set('lastFolderCss', $lastFolderCss);
$fileDownload->Set('firstFileCss', $firstFileCss);
$fileDownload->Set('lastFileCss', $lastFileCss);
$fileDownload->Set('folderCss', $folderCss);
$fileDownload->Set('fileCss', $fileCss);
$fileDownload->Set('parentCss', $parentCss);
$fileDownload->Set('directoryCss', $directoryCss);
$fileDownload->Set('pathCss', $pathCss);
$fileDownload->Set('extCss', $extCss);
$fileDownload->Set('delSuccess', $delSuccess);
$fileDownload->Set('delError', $delError);
$fileDownload->Set('dirOpenError', $dirOpenError);
$fileDownload->Set('notaDir', $notaDir);
$fileDownload->Set('noDownload', $noDownload);
$fileDownload->Set('userFolder', $userFolder);

return $fileDownload->Run();
?>