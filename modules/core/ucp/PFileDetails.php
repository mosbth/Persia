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
// File: PFileDetails.php
//
// Description: Show and edit metadata of a file.
//
// Author: Mikael Roos, mos@bth.se
//
// Known issues:
// -
//
// History: 
// 2010-06-16: Moved from module filearchive to core/ucp. Support both view and edit.
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
$if->UserIsSignedInOrRedirectToSignIn();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
// Always check whats coming in...
//
$userId		= $uc->GetAccountId();
$filename	= strip_tags($pc->GETisSetOrSetDefault('file'));
$mode			= strip_tags($pc->GETisSetOrSetDefault('mode'));


// -------------------------------------------------------------------------------------------
//
// Get file details/metadata from database
//
$mysqli = $db->Connect();
$query 	= <<< EOD
CALL {$db->_['PFileDetails']}('{$userId}', '{$filename}', @success);
SELECT @success AS success;
EOD;
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

// Fetch and use the results
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
// UCP. Include the menu-bar for the User Control Panel.
//
$htmlCp = "";
require(dirname(__FILE__) . '/IUserControlPanel.php');


// -------------------------------------------------------------------------------------------
//
// Create the HTML
//
global $gModule;

$action 	= "?m={$gModule}&amp;p=ucp-filedetailsp";
$redirect = "?m={$gModule}&amp;p=ucp-filedetails&amp;file={$filename}#fedit";

// Get and format messages from session if they are set
$helpers = new CHTMLHelpers();
$messages = $helpers->GetHTMLForSessionMessages(
	Array('success'), 
	Array('failed'));

$hideDeleteButton 	= empty($deleted) ? '' : 'hide' ;
$hideRestoreButton 	= empty($deleted) ? 'hide' : '' ;

$caption = sprintf($pc->lang['FILE_DETAILS_CAPTION'], $name);

$htmlMain = <<<EOD
{$htmlCp}

<div id='fedit' class='section'>
	<form action='{$action}' method='post'>
		<input type='hidden' name='redirect' 					value='{$redirect}'>
		<input type='hidden' name='redirect-failure' 	value='{$redirect}'>
		<input type='hidden' name='fileid' 						value='{$fileid}'>
		
		<fieldset class='standard type-2'>
	 		<legend>{$caption}</legend>
		 	<div class='form-wrapper'>

				<label for="filename">{$pc->lang['FILE_DETAILS_FILENAME']}</label>
				<input name='name' type='text' value='{$name}' maxlength='{$db->_['CSizeFileName']}' autofocus>

				<label for='uniquename'>{$pc->lang['FILE_DETAILS_UNIQUENAME']}</label>
				<input name='uniquename' type='text' value='{$uniquename}' disabled>

				<label for='path'>{$pc->lang['FILE_DETAILS_PATH']}</label>
				<input name='path' type='text' value='{$path}' disabled>
				
				<label for='size'>{$pc->lang['FILE_DETAILS_SIZE']}</label>
				<input name='size' type='text' value='{$size}' disabled>
				
				<label for='mimetype'>{$pc->lang['FILE_DETAILS_MIMETYPE']}</label>
				<input name='mimetype' type='text' value='{$mimetype}' maxlength='{$db->_['CSizeMimetype']}'>
				
				<label for='created'>{$pc->lang['FILE_DETAILS_CREATED']}</label>
				<input name='created' type='datetime' value='{$created}' disabled>
				
				<label for='modified'>{$pc->lang['FILE_DETAILS_MODIFIED']}</label>
				<input name='modified' type='datetime' value='{$modified}' disabled placeholder='{$pc->lang['FILE_TIME_FOR_MODIFIED']}'>
				
				<label for='deleted'>{$pc->lang['FILE_DETAILS_DELETED']}</label>
				<input name='deleted' type='datetime' value='{$deleted}' disabled placeholder='{$pc->lang['FILE_TIME_FOR_DELETED']}'>
				
				<div class='buttonbar'>
					<button type='submit' class='delete {$hideDeleteButton}' name='do-submit' value='delete-file'>{$pc->lang['DELETE_FILE']}</button>
					<button type='submit' class='restore {$hideRestoreButton}' name='do-submit' value='restore-file'>{$pc->lang['RESTORE_FILE']}</button>
					<button type='submit' class='save' name='do-submit' value='save-file-details'>{$pc->lang['FILE_DETAILS_SAVE']}</button>
				</div> <!-- buttonbar -->

				<div class='form-status'>{$messages['success']}{$messages['failed']}</div> 
		 </div> <!-- wrapper -->
		</fieldset>
	</form>
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
CHTMLPage::GetInstance()->PrintPage(sprintf($pc->lang['FILE_DETAILS_TITLE'], $name), $htmlLeft, $htmlMain, $htmlRight);
exit;


?>