<?php
// ===========================================================================================
//
// File: PFileDetailsProcess.php
//
// Description: Save details/metadata about a file.
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
$submitAction	= $pc->POSTisSetOrSetDefault('do-submit');
$redirect			= $pc->POSTisSetOrSetDefault('redirect');
$redirectFail	= $pc->POSTisSetOrSetDefault('redirect-fail');
$userId		= $_SESSION['idUser'];


// -------------------------------------------------------------------------------------------
//
// Depending on the submit-action, do whats to be done. If, else if, else, replaces switch.
// 


// -------------------------------------------------------------------------------------------
//
// Do some insane checking to avoid misusage, errormessage if not correct.
// 
if(false) {

}


// -------------------------------------------------------------------------------------------
//
// Save details/metadata on a file.
// 
else if($submitAction == 'save-file-details') {

	$db = new CDatabaseController();

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
CALL {$db->_['PFileDetailsUpdate']}({$fileid}, '{$userId}', '{$name}', '{$mimetype}');
EOD;

	// Perform the query and assume it all whent okey
	$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);
	$mysqli->close();
	$pc->SetSessionMessage('success', $pc->lang['FILE_DETAILS_UPDATED']);
	$pc->RedirectTo($redirectFail);
}


// -------------------------------------------------------------------------------------------
//
// Default, submit-action not supported, show error and die.
// 
die($pc->lang['SUBMIT_ACTION_NOT_SUPPORTED']);


?>