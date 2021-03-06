<?php
// ===========================================================================================
//
// File: PFileDownload.php
//
// Description: Show details on a file and enable public download.
//
// Author: Mikael Roos, mos@bth.se
//


// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
$pc = new CPageController();
$pc->LoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// Interception Filter, controlling access, authorithy and other checks.
//
$intFilter = new CInterceptionFilter();
$intFilter->FrontControllerIsVisitedOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$file	= $pc->GETisSetOrSetDefault('file');


// -------------------------------------------------------------------------------------------
//
// Get file details/metadata from database
//
$db 		= new CDatabaseController();
$mysqli = $db->Connect();

// Create the query
$file 	= $mysqli->real_escape_string($file);
$query 	= <<< EOD
CALL {$db->_['PFileDetails']}(NULL, '{$file}', @success);
SELECT @success AS success;
EOD;

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

$row = $results[2]->fetch_object();

// If file is not valid then redirect to 403 with a message
if($row->success > 1) {
	$pc->RedirectToModuleAndPage('', 'p403', '', $db->_['FFileCheckPermissionMessages'][$row->success]);
}

$row = $results[0]->fetch_object();
$fileid 		= $row->fileid;
$owner 			= $row->owner;
$name 			= $row->name;
$uniquename = $row->uniquename;
$path 			= $row->path;
$size 			= $row->size;
$mimetype 	= $row->mimetype;
$created 		= $row->created;
$modified 	= $row->modified;
$deleted 		= $row->deleted;

$results[2]->close();
$results[0]->close();
$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Create the HTML
//
$downloadNow = "?m={$gModule}&amp;p=download-now&amp;file={$uniquename}";

$header	 = sprintf($pc->lang['FILE_DOWNLOAD_HEADER'], $name);
$caption = sprintf($pc->lang['FILE_DOWNLOAD_CAPTION'], $created, $owner);

// Start download automatically
$secondsBeforeDownloadStart = 10;
$timeMessage = sprintf($pc->lang['FILE_DOWNLOAD_STARTS_SOON'], $secondsBeforeDownloadStart);

$htmlHead = <<<EOD
<meta http-equiv='refresh' content="{$secondsBeforeDownloadStart};URL='{$downloadNow}'">
EOD;

$htmlMain = <<<EOD
<div class='section'>
<h1>{$header}</h1>
<p>{$pc->lang['FILE_DOWNLOAD_DESCRIPTION']}</p>
</div> <!-- section -->

<div class='section'>
<table class='standard filedetails-show'>
<caption>{$caption}</caption>
<colgroup><col class='header'><col></colgroup>
<thead></thead>
<tbody>
<tr>
<td>{$pc->lang['FILE_DETAILS_FILENAME']}</td>
<td>{$name}</td>
</tr>
<tr>
<td>{$pc->lang['FILE_DETAILS_SIZE']}</td>
<td>{$size}</td>
</tr>
<tr>
<td>{$pc->lang['FILE_DETAILS_MIMETYPE']}</td>
<td>{$mimetype}</td>
</tr>
</tbody>
<tfoot></tfoot>
</table>
</div> <!-- section -->

<div class='section'>
<p>{$timeMessage}</p>
<div class='nav-standard nav-button'>
<ul>
<li><a href='{$downloadNow}'>{$pc->lang['FILE_DOWNLOAD_NOW']}</a>
</ul>
<div class='clear'>&nbsp;</div>
</div>
</div> <!-- section -->


EOD;

$htmlLeft 	= "";
$htmlRight	= <<<EOD
<h3 class='columnMenu'></h3>
<p>
Later...
</p>

EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->PrintPage(sprintf($pc->lang['FILE_DOWNLOAD_TITLE'], $name), $htmlLeft, $htmlMain, $htmlRight, $htmlHead);
exit;

?>