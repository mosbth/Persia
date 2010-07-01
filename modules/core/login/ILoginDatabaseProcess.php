<?php
// ===========================================================================================
//
// File: ILoginDatabaseProcess.php
//
// Description: Submit-action to authenticate user in the local database. 
// To be included in a pagecontroller that has the correct environment set.
//
// Author: Mikael Roos, mos@bth.se
//
// Preconditions:
//
// Variables must be defined by pagecontroller:
// $db
// $mysqli
// $account 
// $accountId (OUT, id of the account if successful) 
// $password
// $redirectFail
//
// Require from pagecontroller using:
// require(TP_MODULESPATH . '/core/login/ILoginDatabaseProcess.php');
//
// Messages that may be set in session reflecting the outcome of the action:
// loginFailed
// loginAccount
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


// -------------------------------------------------------------------------------------------
//
// Perform authentication using account and password stored in local database
//
$account 	= $mysqli->real_escape_string($account);
$password = $mysqli->real_escape_string($password);

$query = <<<EOD
CALL {$db->_['PAuthenticateAccount']}(@accountId, '{$account}', '{$password}', @status);
SELECT 
	@accountId AS accountid,
	@status AS status;
EOD;

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

// Get details from resultset
$row = $results[1]->fetch_object();

if($row->status == 1) {
	$pc->SetSessionMessage('loginFailed', $pc->lang['AUTHENTICATION_FAILED']);
	$pc->SetSessionMessage('loginAccount', $account);
	$pc->RedirectTo($redirectFail);	
}

// Set the id of the authenticated account
$accountId = $row->accountid;

// Clean up
$results[1]->close();


?>