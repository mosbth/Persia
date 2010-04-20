<?php
// ===========================================================================================
//
// File: PAccountSettingsProcess.php
//
// Description: Save changes in profile and account settings.
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
//$intFilter->UserIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$submitAction	= $pc->POSTisSetOrSetDefault('submit');
$accountId		= $pc->POSTisSetOrSetDefault('accountid');
$redirect			= $pc->POSTisSetOrSetDefault('redirect');
$redirectFail	= $pc->POSTisSetOrSetDefault('redirect-failure');
$userId				= $_SESSION['idUser'];

// Always check whats coming in...
//$pc->IsNumericOrDie($topicId, 0);


// -------------------------------------------------------------------------------------------
//
// Depending on the submit-action, do whats to be done. If, else if, else, replaces switch.
// 


// -------------------------------------------------------------------------------------------
//
// Do some insane checking to avoid misusage, errormessage if not correct.
// 
// Are we trying to change the same user profile as is signed in? Must be Yes.
//
if($userId != $accountId) {
		$pc->SetSessionErrorMessage($pc->lang['MISMATCH_SESSION_AND_SETTINGS']);
		$pc->RedirectTo($redirectFail);
}


// -------------------------------------------------------------------------------------------
//
// Change the password
// 
else if($submitAction == 'change-password') {

	$password1	= $pc->POSTisSetOrSetDefault('password1');
	$password2	= $pc->POSTisSetOrSetDefault('password2');

	if(empty($password1) || empty($password2)) {
		$pc->SetSessionErrorMessage($pc->lang['PASSWORD_CANNOT_BE_EMPTY']);
		$pc->RedirectTo($redirectFail);
	} 
	else if($password1 != $password2) {
		$pc->SetSessionErrorMessage($pc->lang['PASSWORD_DOESNT_MATCH']);
		$pc->RedirectTo($redirectFail);
	}

	// Execute the database query to make the update
	$db = new CDatabaseController();
	$mysqli = $db->Connect();

	// Get the SP names
	$spChangePassword	= DBSP_PChangeAccountPassword;

	$query  = sprintf("CALL {$spChangePassword}(%s, '%s');", $userId, $mysqli->real_escape_string($password1));
	$query .= "SELECT ROW_COUNT() AS rowsaffected;";
	
	// Perform the query
	$results = Array();
	$res = $db->MultiQuery($query); 
	$db->RetrieveAndStoreResultsFromMultiQuery($results);

	// Get details from resultset
	$row = $results[1]->fetch_object();

	/*
	if($row->rowsaffected != 1) {
		$pc->SetSessionErrorMessage($pc->lang['PASSWORD_WAS_NOT_UPDATED']);
		$pc->RedirectTo($redirectFail);
	} 
	*/
	
	$results[1]->close();
	$mysqli->close();

	// Redirect to resultpage
	$pc->RedirectTo($redirect);
}


// -------------------------------------------------------------------------------------------
//
// Change email
// 
else if($submitAction == 'change-email') {

	$email	= $pc->POSTisSetOrSetDefault('email');

	// Execute the database query to make the update
	$db = new CDatabaseController();
	$mysqli = $db->Connect();

	// Get the SP names
	$spChangeEmail	= DBSP_PChangeAccountEmail;

	$query  = sprintf("CALL {$spChangeEmail}(%s, '%s');", $userId, $mysqli->real_escape_string($email));
	$query .= "SELECT ROW_COUNT() AS rowsaffected;";
	
	// Perform the query
	$results = Array();
	$res = $db->MultiQuery($query); 
	$db->RetrieveAndStoreResultsFromMultiQuery($results);

	// Get details from resultset
	$row = $results[1]->fetch_object();

	/*
	if($row->rowsaffected != 1) {
		$pc->SetSessionErrorMessage($pc->lang['EMAIL_WAS_NOT_UPDATED']);
		$pc->RedirectTo($redirectFail);
	} 
	*/
	
	$results[1]->close();
	$mysqli->close();

	// Redirect to resultpage
	$pc->RedirectTo($redirect);
}


// -------------------------------------------------------------------------------------------
//
// Change avatar
// 
else if($submitAction == 'change-avatar') {

	$avatar	= $pc->POSTisSetOrSetDefault('avatar');

	// Execute the database query to make the update
	$db = new CDatabaseController();
	$mysqli = $db->Connect();

	// Get the SP names
	$spChangeAvatar	= DBSP_PChangeAccountAvatar;

	$query  = sprintf("CALL {$spChangeAvatar}(%s, '%s');", $userId, $mysqli->real_escape_string($avatar));
	$query .= "SELECT ROW_COUNT() AS rowsaffected;";
	
	// Perform the query
	$results = Array();
	$res = $db->MultiQuery($query); 
	$db->RetrieveAndStoreResultsFromMultiQuery($results);

	// Get details from resultset
	$row = $results[1]->fetch_object();

	/*
	if($row->rowsaffected != 1) {
		$pc->SetSessionErrorMessage($pc->lang['AVATAR_WAS_NOT_UPDATED']);
		$pc->RedirectTo($redirectFail);
	} 
	*/
	
	$results[1]->close();
	$mysqli->close();

	// Redirect to resultpage
	$pc->RedirectTo($redirect);
}


// -------------------------------------------------------------------------------------------
//
// Default, submit-action not supported, show error and die.
// 
die($pc->SetSessionErrorMessage($pc->lang['SUBMIT_ACTION_NOT_SUPPORTED']));


?>