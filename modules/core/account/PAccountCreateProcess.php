<?php
// ===========================================================================================
//
// File: PAccountCreateProcess.php
//
// Description: Create a new account.
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
//$intFilter->UserIsSignedInOrRecirectToSignIn();
//$intFilter->UserIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$submitAction	= $pc->POSTisSetOrSetDefault('submit');
$redirect			= $pc->POSTisSetOrSetDefault('redirect');
$redirectFail	= $pc->POSTisSetOrSetDefault('redirect-fail');
$silentLogin	= $pc->POSTisSetOrSetDefault('silent-login');

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
if(false) {

}


// -------------------------------------------------------------------------------------------
//
// Change the account
// 
else if($submitAction == 'account-create') {

	// Get the input and check it
	$account		= $pc->POSTisSetOrSetDefault('account');
	$password1	= $pc->POSTisSetOrSetDefault('password1');
	$password2	= $pc->POSTisSetOrSetDefault('password2');

	//
	// Check the CAPTCHA
	//
	$captcha = new CCaptcha();
	if(!$captcha->CheckAnswer()) {
		$pc->SetSessionErrorMessage($pc->lang['CAPTCHA_FAILED']);
		$pc->RedirectTo($redirectFail);		
	}

	//
	// Check the passwords
	//
	if(empty($password1) || empty($password2)) {
		$pc->SetSessionErrorMessage($pc->lang['PASSWORD_CANNOT_BE_EMPTY']);
		$pc->RedirectTo($redirectFail);
	} 
	else if($password1 != $password2) {
		$pc->SetSessionErrorMessage($pc->lang['PASSWORD_DOESNT_MATCH']);
		$pc->RedirectTo($redirectFail);
	}

	//
	// Execute the database query to make the update
	//
	$db = new CDatabaseController();
	$mysqli = $db->Connect();

	// Prepare query
	$account 	= $mysqli->real_escape_string($account);
	$password = $mysqli->real_escape_string($password1);
	$hashingalgoritm = DB_PASSWORDHASHING;

	$query = <<<EOD
CALL {$db->_['PCreateAccount']}(@accountId, '{$account}', '{$password}', '{$hashingalgoritm}', @status);
SELECT 
	@accountId AS accountid,
	@status AS status;
EOD;

	// Perform the query
	$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

	// Get details from resultset
	$row = $results[1]->fetch_object();

	if($row->status == 1) {
		$pc->SetSessionErrorMessage($pc->lang['ACCOUNTNAME_ALREADY_EXISTS']);
		$pc->RedirectTo($redirectFail);	
	}
	
	$results[1]->close();
	$mysqli->close();

	//
	// Do a silent login and then proceed to $redirect
	//
	$_SESSION['silentLoginAccount'] 	= $account;
	$_SESSION['silentLoginPassword'] 	= $password;
	$_SESSION['silentLoginRedirect'] 	= $redirect;
	$pc->RedirectTo($silentLogin);
}


// -------------------------------------------------------------------------------------------
//
// Default, submit-action not supported, show error and die.
// 
die($pc->lang['SUBMIT_ACTION_NOT_SUPPORTED']);


?>