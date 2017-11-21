# THIS PROJECT IS DEPRECATED

FileDownload is not maintained anymore. It maybe does not work in Evolution 1.1 anymore. Please fork it and bring it back to life, if you need it.

# FileDownload

## Part 1: Snippet

* Snippet created by [Kyle Jaebker](http://www.muddydogpaws.com)
* Short Desc: Lists files from a folder(s) for downloading.
* Version: 2.7
* Last Modified: 21/05/2013

### Snippet Install Instructions
		
1. Copy the `filedownload` folder to `assets/snippets`.
2. Create a new snippet named FileDownload and and paste the following line
as snippet code `<?php return include(MODX_BASE_PATH.'assets/snippets/filedownload/filedownload.snippet.php'); ?>`
3. Use the snippet call to customize the display of the download list.
		
### Snippet Usage
```
[!FileDownload? &getFolder=`assets/snippets/filedownload`!]
```
	
#### Basic Parameters

Name | Description | Default
---- | ----------- | -------
getFolder | The getFolder parameter is used to specify which directories to display with the snippet. Multiple directories can be specified by seperating them with a comma. When specifying multiple directories the directory browsing functionality is no longer available. When typing a path do not include a trailing /. (CAUTION: If the plugin is used for downloading the value of getFolder is set by the template variable FileDownloadFolder and not by the snippet call). | -
browseDirectories | The browseDirectories parameter allows users to view subdirectories of the directory specified with the getFolder parameter. When using this feature the following templates get used: parent & folder. | 0 
groupByDirectory | When multiple directories are specified in the getFolder parameter, this parameter will group the files by folder. When grouped by folder, the directory template is used to output the path above each group. | 0
getFile | The getFile parameter will make the snippet output only the file specified. The getFolder parameter is still required and getFile should be a file inside of the directory. This allows for use of the download script and download counting with a single file. | -
chkDesc | The chkDesc parameter allows descriptions to be added to the file listing included in a chunk. All of the files and descriptions should be listed in the chunk using the following format: path to file/filename&#124;description&#124;&#124; | - (example: `chkDesc.txt` in folder `templates`)
userSort | The userSort parameter allows the outputted files to be sorted by all of the fields listed above. To sort by multiple fields use a comma delimited list. When using the directory browsing feature the files will be sorted by type first, this will put the directories first in the list. When multiple folders are specified and the group by directory feature is used; the files are sorted by path first to keep the files in order by directory. Possible values: filename  &#124; extension  &#124; path  &#124; size  &#124; sizetext  &#124; type  &#124; date  &#124; description  &#124; count | filename (enabled browseDirectories: type,filename; enabled groupByDirectory: path,filename)
sortOrder | The sortOrder parameter makes it possible to sort files in ascending or descending order. | asc
showExt | The showExt parameter will limit the files displayed to files with a valid extension from the list. | -
hideExt | The hideExt parameter will remove any of the specified extensions from the output. | -
dateFormat | The dateFormat parameter will change the format of the date displayed for each file in the output. | Y-m-d
userFolder | The userFolder parameter is used to activate the change of the displayed directory by `getFolder` parameter or `FileDownloadFolder` template variable to a subfolder with the username of a logged webuser | 0

#### Permission Parameters

Name | Description | Default
---- | ----------- | -------
downloadGroups | The downloadGroups parameter will make the download link active for users that belong to the specified groups. If a user is not logged in they will receive a JavaScript alert with the message contained in the noDownload language setting. Multiple groups can be specified by using a comma delimited list. | -
deleteGroups | The parameter deleteGroups allows the specified web user groups to delete files from the listing. The link will only be displayed for users that are logged in and in the specified web user group. | -

#### Counting Parameters

Name | Description | Default
---- | ----------- | -------
countDownloads | With the countDownloads parameter set to 1, everytime a user downloads a file it will be tracked in a database table (fd_count) or in a file (fileCount). | 1
useDbCount | The useDbCount parameter allows the count information to be stored in wither the database or in a file. The defualt setting is to include the counts in the database, to store the counts in a file set this to 0. | 1
skipTableCheck | When using the database for counting downloads set this to 1 for an added speed boost. This will eliminate the check to see if the count table needs added. Do not set this to 1 when running the snippet for the first time, or the table will not get added. | 0

#### Image Parameters

Name | Description | Default
---- | ----------- | -------
imgLocat | The imgLocat parameter holds the path to the images to associate with each file extension. The images will be outputted with [+fd.image+] placeholder. | -
imgTypes | The imgTypes parameter allows for associations between file extensions and an image. The information on these associations should be put into a chunk similar to the example below. Associations should be in a comma delimited list with an equal sign between the extension and the image name. The parent extension is used for getting the image for the parent folder link when using directory browsing. The folder extension is used for getting the image to associate with a directory. The default extension is applied to all files with extensions not specified in the chunk. | - (example: `imgTypes.txt` in folder `templates`)

#### Template Parameters

Name | Description | Default
---- | ----------- | -------
tplList | The templating for filedownload is all handled with one chunk. This chunk is split into seven sections: header, parent, folder, file, delete link, group by, and footer. All of these sections should be in the chunk template even if they are not all being used or it will not be processed correctly. | `filedownload.template.html` in folder `templates`
splitter | The splitter parameter should be set to the string used to seperate your templates in the chunk. If the default splitter is used or the default template is used this does not need changed. | &lt;!-- Fd:Splitter --&gt;
altCss | This parameter specifies the class that will be applied to every other file/folder so a ledger look can be styled. | fd-alt
firstFolderCss | This parameter specifies the class that will be applied to the first folder. | -
lastFolderCss | This parameter specifies the class that will be applied to the last folder. | -
firstFileCss | This parameter specifies the class that will be applied to the first file. | -
lastFileCss | This parameter specifies the class that will be applied to the last file. | -
folderCss | This parameter specifies the class that will be applied to all folders. | fd-folder
fileCss | This parameter specifies the class that will be applied to all files. | fd-file
parentCss | This parameter specifies the class that will be applied to the parent item. | fd-parent
directoryCss | This parameter specifies the class that will be applied to the directory for multi-folder grouping. | fd-directory
pathCss | This parameter specifies the class that will be applied to the path when using directory browsing. | fd-path
extCss | With this parameter set to 1 a class will be added to each file with the files extension. For example, a pdf would get the class: fd-pdf. | 0

#### Language Parameters

Name | Description | Default
---- | ----------- | -------
delSuccess | This message will be displayed after the successful deletion of a file. The name of the file will be appended to the end of the message. | You have deleted:
delError | This message will be displayed if there was an error deleting a file. The name of the file will be appended to the end of the message. | There was an error deleting:
dirOpenError | This message will be displayed if the directory specified in the getFolder parameter cannot be opened. | Cannot open the directory:
notaDir | This message will be displayed if the directory specified in the getFolder parameter is not a directory. | The path specified is not a directory:
noDownload | This message will be inserted into the javascript alert if a user does not have access to download a file. | You do not have permission to download this file.

#### Template Placeholder

Name | Description | Template
---- | ----------- | --------
fd.class | Contain the table row classes of the table specified by template parameters | header, parent, folder, file, directory
fd.path | Contains the path of the current directory | header
fd.image | Contains the icon for the current file/folder | parent, folder, file
fd.link | Contains the download/folderchange link to the current displayed file/folder | parent, folder, file
fd.filename | Contains the name of the current displayed file/folder | file
fd.extension | Contains the extension of the current displayed file/folder | file
fd.count | Contains the download count of the current displayed file | file
fd.delete | Contains the rendered delete template for the current displayed file (if deleting is allowed) | file
fd.sizetext | Contains the human readable download size of the current displayed file | parent, file
fd.size | Contains the download size of the current displayed file in bytes | parent, file
fd.date | Contains the human readable date of the current displayed file formatted according to `dateFormat` parameter | file
fd.unixdate | Contains the unixtime date of the current displayed file | file
fd.description | Contains the file description of the current displayed file given by chunk referenced in `chkDesc` parameter | file
fd.deletelink | Contains the link to delete the current displayed file (if deleting is allowed) | delete link
fd.directory | Contains the path of the directory if parameter `groupByDirectory` is active | directory
fd.filenumber | Contains the number of the current file/folder | file 
fd.iteration | Contains the number of the current displayed table row | header, parent, folder, file, directory, footer

## Part 2: Plugin

* Plugin created by Adam Strzelecki
* Short Desc: File downloads with respect to document's permissions
* Version: 1.3
* Last Modified: 21/05/2013

The Plugin lets you bind a downloads folder to specific document using TV with folder path, and drive the file downloads with respect to document's permissions, large files, HTTP partial downloads, ETags, using i.e.:

`http://www.mymodxsite.com/downloads.html/abao0995u.exe`
`http://www.mymodxsite.com/downloads.html&d=abao0995u.exe`
`http://www.mymodxsite.com/index?q=1&d=abao0995u.exe`

The reason I wrote that plugin was that I needed secure downloads within the MODx site.

I've used FileDownload snippet, however attached to that download.php script was not respecting document's privileges, moreover it was exposing real (relative) paths of the files in the base64 encoded URL.

FileDownload snippet inspired me to write this plugin. Content type check code is being taken from FileDownload snippet (GPL).

My plugin exposes files binded to the document with TV variable `FileDownloadFolder`. All files are relative paths to relative or abolute path specified in `FileDownloadFolder, while traverse up with `..` is not allowed.

Therefore user downloading files is not aware of the physical placement of those files. This placement can be even away the MODx install root or `assets`, letting you bind some network `build` folder to the document.

If the file specified with `&d=` query parameter is found and accessible it is being streamed to the client, if the `&d=` file is invalid, no TV specified or it is not accessible the content of "parent" document is sent as if `&d=` was never specified.

FileDownloadPlugin features:

* handling file download relative to the document's TV `FileDownloadFolder`
* respecting document's privileges, since plugin code is not called if user has no document permissions physical paths are unknown to the user, and URL's are served friendly way
* support for large files, no timeouts, low (configurable) memory usage
* support for partial downloads HTTP/206 Partial Content used by download boosters
* support for modification/cache checking with HTTP/304 Not Modified
* support for long download, checks if the client has disconnected

This plugin integrates with FileDownload snippet. You can use the snippet for embedding the file list onto the page while the plugin will be serving the downloads.

### Plugin install instructions

1. Create new TV called `FileDownloadFolder` in the MODx Manager
2. Create a new plugin called `FileDownloadPlugin` and and paste the following line 
as plugin code `include(MODX_BASE_PATH.'assets/plugins/filedownload/filedownload.plugin.php');`
3. Set the plugin configuration to `&countDownloads=Count Downloads;list;yes,no;yes &useDbCount=Store Count Where;list;db,file;db`
4. Bind the plugin to `OnWebPageInit` event
5. Save the plugin
6. If you use YAMS, make sure that `FileDownloadPlugin` is executed before `YAMS` in `OnWebPageInit`

You may now start using plugin. Create a new document called `downloads` and set its TV `FileDownloadFolder` for instance to `/var/www/domain/downloads` path containing `myzipfile.zip`.

From now `http://www.mymodxsite.com/index?q=downloads&d=myzipfile.zip` will stream the client `/var/www/domain/downloads/myzipfile.zip` if only the client has access to `downloads` document.

If you use friendly URLs you should also add the following line to your ".htaccess" rewrite rule before the MODX Friendly URLs part.

```
RewriteRule ^(download)/(.+)$ index.php?q=$1&d=$2 [L,QSA]
```
With this line the file will be downloadable on: `http://www.mymodxsite.com/download/myzipfile.zip`

Change the word `download` to the alias or the alias path of document showing the downloads. 

If you want you can use different variable name, memory block size for the plugin or webuser based foldernames, just use the extended extended plugin configuration with the following string and edit this configuration:

```
&countDownloads=Count Downloads;list;yes,no;yes
&useDbCount=Store Count Where;list;db,file;db 
&blockSize=Block Size;text;262144 
&fileDownloadFolderTV=File Download Folder Template Variable;text;FileDownloadFolder
&userFolder=Webuser based foldername;list;yes,no;no
```
