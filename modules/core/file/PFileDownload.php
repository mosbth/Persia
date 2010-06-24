<?php
// ===========================================================================================
//
//		Persia (http://phpersia.org), software to build webbapplications.
//    Copyright (C) 2010  Mikael Roos (mos@bth.se)
//
//    This file is part of Persia.
//
//    Persia is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    Persia is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with Persia. If not, see <http://www.gnu.org/licenses/>.
//
// File: PFileDownload.php
//
// Description: Show details on a file and enable public download.
//
// Author: Mikael Roos, mos@bth.se
//
// Known issues:
// -
//
// History: 
// 2010-06-24: Moved to core. Included license message.
//


// -------------------------------------------------------------------------------------------
//
// Get common controllers, uncomment if not used in current pagecontroller.
//
// $pc, Page Controller helpers. Useful methods to use in most pagecontrollers
// $uc, User Controller. Keeps information/permission on user currently signed in.
// $if, Interception Filter. Useful to check constraints before entering a pagecontroller.
// $db, Database Controller. Manages all database access.
//
$pc = CPageController::GetInstanceAndLoadLanguage(__FILE__);
$uc = CUserController::GetInstance();
$if = CInterceptionFilter::GetInstance();
$db = CDatabaseController::GetInstance();


// -------------------------------------------------------------------------------------------
//
// Perform checks before continuing, what's to be fullfilled to enter this controller?
//
$if->FrontControllerIsVisitedOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
// Always check whats coming in...
//
$file	= $pc->GETisSetOrSetDefault('file');


// -------------------------------------------------------------------------------------------
//
// Get file details/metadata from database
//
$mysqli = $db->Connect();

// Create the query
$file 	= $mysqli->real_escape_string($file);
$query 	= <<< EOD
CALL {$db->_['PFileDetails']}(NULL, '{$file}', @success);
SELECT @success AS success;
EOD;

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

// Take care of the results
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
	<p>{$timeMessage}</p>
	<ul class='nav-standard nav-button'>
		<li><a href='{$downloadNow}'>{$pc->lang['FILE_DOWNLOAD_NOW']}</a>
	</ul>
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


EOD;

$htmlLeft 	= "";
$htmlRight	= <<<EOD
<!--
<h3 class='columnMenu'></h3>
<p>
Later...
</p>
-->

EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
CHTMLPage::GetInstance()->printPage(sprintf($pc->lang['FILE_DOWNLOAD_TITLE'], $name), $htmlLeft, $htmlMain, $htmlRight, $htmlHead);
exit;


?>