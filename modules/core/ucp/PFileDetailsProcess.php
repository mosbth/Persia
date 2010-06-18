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
// File: PFileDetailsProcess.php
//
// Description: Save details/metadata about a file.
//
// Known issues:
// -
//
// History: 
// 2010-06-18: Created.
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
//$userName	= $uc->GetAccountName();

$submitAction	= $pc->POSTisSetOrSetDefault('do-submit');
$redirect			= $pc->POSTisSetOrSetDefault('redirect');
$redirectFail	= $pc->POSTisSetOrSetDefault('redirect-fail');


// -------------------------------------------------------------------------------------------
//
// Depending on the submit-action, do whats to be done. If, else if, else, replaces switch.
// Start by doing some insane checking to avoid misusage, errormessage if not correct.
// 
if(false) {

}


// -------------------------------------------------------------------------------------------
//
// Save details/metadata on a file.
// 
else if($submitAction == 'save-file-details') {

	// Get the input
	$fileid		= $pc->POSTisSetOrSetDefault('fileid');
	$name 		= $pc->POSTisSetOrSetDefault('name');
	$mimetype = $pc->POSTisSetOrSetDefault('mimetype');

	// Check boundaries for whats coming in
	// is name within size?
	if(!(is_numeric($fileid) && $fileid > 0)) {
		$pc->SetSessionMessage('failed', $pc->lang['FILEID_INVALID']);
		$pc->RedirectTo($redirectFail);
	}

	// is name within size?
	if(mb_strlen($name) > $db->_['CSizeFileName']) {
		$pc->SetSessionMessage('failed', sprintf($pc->lang['FILENAME_TO_LONG'], $db->_['CSizeFileName']));
		$pc->RedirectTo($redirectFail);
	}

	// is mimetype within size?
	if(mb_strlen($mimetype) > $db->_['CSizeMimetype']) {
		$pc->SetSessionMessage('failed', sprintf($pc->lang['MIMETYPE_TO_LONG'], $db->_['CSizeMimetype']));
		$pc->RedirectTo($redirectFail);
	}
	
	// Save metadata of the file in the database
	$mysqli = $db->Connect();

	// Create the query
	$query 	= <<< EOD
CALL {$db->_['PFileDetailsUpdate']}({$fileid}, '{$userId}', '{$name}', '{$mimetype}', @success);
SELECT @success AS success;
EOD;

	// Perform the query and manage results
	$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);
	
	$row = $results[1]->fetch_object();
	if($row->success) {
		$pc->SetSessionMessage('failed', $db->_['FFileCheckPermissionMessages'][$row->success]);
		$pc->RedirectTo($redirectFail);
	}

	$results[1]->close();
	$mysqli->close();
	
	$pc->SetSessionMessage('success', $pc->lang['FILE_DETAILS_UPDATED']);
	$pc->RedirectTo($redirect);
}


// -------------------------------------------------------------------------------------------
//
// Set a file to be deleted/not deleted.
// 
else if($submitAction == 'delete-file' || $submitAction == 'restore-file') {

	// Get the input
	$fileid		= $pc->POSTisSetOrSetDefault('fileid');
	$deleteOrRestore = ($submitAction == 'delete-file') ? 1 : (($submitAction == 'restore-file') ? 2 : 0);
	
	// Save metadata of the file in the database
	$mysqli = $db->Connect();

	// Create the query
	$query 	= <<< EOD
CALL {$db->_['PFileDetailsDeleted']}({$fileid}, '{$userId}', '{$deleteOrRestore}', @success);
SELECT @success AS success;
EOD;

	// Perform the query and manage results
	$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);
	
	$row = $results[1]->fetch_object();
	if($row->success) {
		$pc->SetSessionMessage('failed', $db->_['FFileCheckPermissionMessages'][$row->success]);
		$pc->RedirectTo($redirectFail);
	}

	$results[1]->close();
	$mysqli->close();
	
	$pc->SetSessionMessage('success', $pc->lang['FILE_DETAILS_UPDATED']);
	$pc->RedirectTo($redirect);
}


// -------------------------------------------------------------------------------------------
//
// Default, submit-action not supported, show error and die.
// 
die($pc->lang['SUBMIT_ACTION_NOT_SUPPORTED']);


?>