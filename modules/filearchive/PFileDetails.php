<?php
// ===========================================================================================
//
// File: PFileDetails.php
//
// Description: Show metadata of users file. Enable to download and edit file.
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
$intFilter->UserIsSignedInOrRecirectToSignIn();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$filename	= $pc->GETisSetOrSetDefault('file');
$userId		= $_SESSION['idUser'];


// -------------------------------------------------------------------------------------------
//
// Get file details/metadata from database
//
$db 		= new CDatabaseController();
$mysqli = $db->Connect();

// Create the query
$query 	= <<< EOD
CALL {$db->_['PFileDetails']}('{$userId}', '{$filename}', @success);
SELECT @success AS success;
EOD;

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

$row = $results[2]->fetch_object();

// If file is not valid then redirect to 403 with a message
if($row->success) {
	$pc->RedirectToModuleAndPage('', 'p403', '', $db->_['FFileCheckPermissionMessages'][$row->success]);
}

$row = $results[0]->fetch_object();
$fileid 		= $row->fileid;
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
global $gModule;

$editDetails 	= "?m={$gModule}&amp;p=file-details-edit&amp;file={$uniquename}";
$download 		= "?m={$gModule}&amp;p=download&amp;file={$uniquename}";

$caption = sprintf($pc->lang['FILE_DETAILS_CAPTION'], $name);

$htmlMain = <<<EOD
<div class='section'>
<h1>{$pc->lang['FILE_DETAILS_HEADER']}</h1>
<p>{$pc->lang['FILE_DETAILS_DESCRIPTION']}</p>

<div class='nav-standard'>
<ul>
<li><a href='{$editDetails}'>{$pc->lang['FILE_DETAILS_EDIT']}</a> 
<li><a href='{$download}'>{$pc->lang['FILE_DOWNLOAD_PAGE']}</a>
</ul>
<div class='clear'>&nbsp;</div>
</div>
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
<td>{$pc->lang['FILE_DETAILS_UNIQUENAME']}</td>
<td title='{$path}'>{$uniquename}</td>
</tr>
<tr>
<td>{$pc->lang['FILE_DETAILS_PATH']}</td>
<td>{$path}</td>
</tr>
<tr>
<td>{$pc->lang['FILE_DETAILS_SIZE']}</td>
<td>{$size}</td>
</tr>
<tr>
<td>{$pc->lang['FILE_DETAILS_MIMETYPE']}</td>
<td>{$mimetype}</td>
</tr>
<tr>
<td>{$pc->lang['FILE_DETAILS_CREATED']}</td>
<td>{$created}</td>
</tr>
<tr>
<td>{$pc->lang['FILE_DETAILS_MODIFIED']}</td>
<td>{$modified}</td>
</tr>
<tr>
<td>{$pc->lang['FILE_DETAILS_DELETED']}</td>
<td>{$deleted}</td>
</tr>
</tbody>
<tfoot></tfoot>
</table>
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

$page->PrintPage($pc->lang['FILE_DETAILS_TITLE'], $htmlLeft, $htmlMain, $htmlRight);
exit;

?>