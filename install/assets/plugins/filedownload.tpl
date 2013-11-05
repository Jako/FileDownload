//<?php
/**
 * FileDownload
 *
 * File downloads with respect to document's permissions
 *
 * @category 	plugin
 * @version 	1.3
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @author      Adam Strzelecki
 * @internal    @properties &countDownloads=Count Downloads;list;yes,no;yes &useDbCount=Store Count Where;list;db,file;db &blockSize=Block Size;text;262144 &fileDownloadFolderTV=File Download Folder Template Variable;text;FileDownloadFolder &userFolder=Webuser based foldername;list;yes,no;no
 * @internal    @events OnWebPageInit
 * @internal    @modx_category Content
 * @internal    @installset base, sample
 */
require(MODX_BASE_PATH . 'assets/snippets/filedownload/filedownload.plugin.php');
