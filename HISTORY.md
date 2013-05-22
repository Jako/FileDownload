# History

- 2.7
    - PHP5/MySQL fixes
    - PHx capable with the use of evoChunkie
    - Some new placeholder in template chunk
    - Bundled with FileDownloadPlugin
    - Plugin event on changed to OnWebPageInit (to play nice with YAMS)
    - Webuser shortname based subfolders in `FileDownloadFolder` possible

- 2.6
    - Fixed error when deleting files in a sub-directory.
    - Added new parameter hideExt. This allows specified extensions to be removed from the output. (The parameter showExt takes precedence over this parameter)
    - Changed both hideExt and showExt parameters to accept a comma delimited list of extensions.
    
- 2.5
    - Refactored to use Adam Strzeleckis (OnO) FileDownload plugin for more secure downloads. Get the plugin [here](http://modx.com/extras/package/filedownloadplugin) with the following features:
	    - Allows for download counting.
	    - Works with subfolder directory browsing.
	    - Works with multiple folders in tvar.
    - If the plugin is used the getFolder parameter is set by the template variable FileDownloadFolder and not the snippet call.
    - No longer uses the download.php file as it created vulnerabilities in the MODx installation. Delete this file from your install.
    - Snippet functions without download counting if you do not want to use the plugin.
    - New parameter (&dateFormat) to format the date of the output. Use PHP's date formatting, to customize it.
		
- 2.0		
    - Completly rewritten with 'OOP' goodness.
    - Ability to specify multiple folders for display in one snippet call.
    - Expanded template support, now with templates for folders and files.
    - Ability to store download counts in databse or file.
    - Parameters to add custom classes to the templates (including extension class).
    - Extra placeholders available for more output options.
    - Image - Extension associations now stored in a chunk so you can set different images per snippet call.
    - Ability to use the download script without download counting.
    - The file size can be passed to the download script to display download progress.
    - And many other changes ...
