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
// File: PTemplateProcess.php
//
// Description: Template showing off how the processing-pagecontrollers usually look like.
//
// Author: Mikael Roos, mos@bth.se
//
// Known issues:
// Update to include and show off "all" features.
//
// History: 
// 2010-06-16: Created.
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
$userName	= $uc->GetAccountName();

$submitAction	= $pc->POSTisSetOrSetDefault('do-submit');
//$redirect			= $pc->POSTisSetOrSetDefault('redirect');
//$redirectFail	= $pc->POSTisSetOrSetDefault('redirect-fail');


// -------------------------------------------------------------------------------------------
//
// Depending on the submit-action, do whats to be done. If, else if, else, replaces switch.
// Start by doing some insane checking to avoid misusage, errormessage if not correct.
// 
if(false) {

}


// -------------------------------------------------------------------------------------------
//
// Upload single file and return html success/failure message. Ajax-like.
// 
else if($submitAction == 'upload-return-html') {

/*
	// http://www.php.net/manual/en/features.file-upload.errors.php
	$errorMessages = Array (
		UPLOAD_ERR_INI_SIZE 	=> $pc->lang['UPLOAD_ERR_INI_SIZE'],
		UPLOAD_ERR_FORM_SIZE 	=> $pc->lang['UPLOAD_ERR_FORM_SIZE'],
		UPLOAD_ERR_PARTIAL 		=> $pc->lang['UPLOAD_ERR_PARTIAL'],
		UPLOAD_ERR_NO_FILE 		=> $pc->lang['UPLOAD_ERR_NO_FILE'],
		UPLOAD_ERR_NO_TMP_DIR => $pc->lang['UPLOAD_ERR_NO_TMP_DIR'],
		UPLOAD_ERR_CANT_WRITE => $pc->lang['UPLOAD_ERR_CANT_WRITE'],
		UPLOAD_ERR_EXTENSION 	=> $pc->lang['UPLOAD_ERR_EXTENSION'],		
	);
	
	// Check that uploaded filesize is within limit
	if ($_FILES['file']['size'] > FILE_MAX_SIZE) {
		exit(CHTMLHelpers::GetHTMLUserFeedbackNegative(sprintf($pc->lang['FILE_UPLOAD_FAILED_MAXSIZE'], FILE_MAX_SIZE)));
	}

	// Create a unique filename
	do {
		$file = uniqid();
		$path = $archivePath . $file;
	} while(file_exists($path));

	// Move the uploaded file
	if (!move_uploaded_file($_FILES['file']['tmp_name'], $archivePath . $file)) {
		exit(CHTMLHelpers::GetHTMLUserFeedbackNegative(sprintf($pc->lang['FILE_UPLOAD_FAILED'], $_FILES['file']['error'], $errorMessages[$_FILES['file']['error']])));
	}
	
	// Store metadata of the file in the database
	$mysqli = $db->Connect();
	$query 	= <<< EOD
CALL {$db->_['PInsertFile']}('{$userId}', '{$_FILES['file']['name']}', '{$path}', '{$file}', {$_FILES['file']['size']}, '{$_FILES['file']['type']}', @fileId, @status);
SELECT @fileId AS fileid, @status AS status;
EOD;
	$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

	// Check if the unique key was accepted, else, create a new one and try again
	$row = $results[1]->fetch_object();
	$status = $row->status;
	$fileid = $row->fileid;
	$results[1]->close();

	// Did the unique key update correctly?	
	if($row->status) {
		// Create query to set new unique name
		do {
			$newid = uniqid();
			$query 	= <<< EOD
CALL {$db->_['PFileUpdateUniqueName']}('{$fileid}', '{$newid}', @status);
SELECT @status AS status;
EOD;

			$row 		= $results[1]->fetch_object();
			$status = $row->status;
			$results[1]->close();
		} while ($status != 0);
	}

	$mysqli->close();

	// Echo out the result
	exit(CHTMLHelpers::GetHTMLUserFeedbackPositive(sprintf($pc->lang['FILE_UPLOAD_SUCCESS'], $_FILES['file']['name'], $_FILES['file']['size'], $_FILES['file']['type'])));
*/
}


// -------------------------------------------------------------------------------------------
//
// Default, submit-action not supported, show error and die.
// 
die($pc->lang['SUBMIT_ACTION_NOT_SUPPORTED']);


?>