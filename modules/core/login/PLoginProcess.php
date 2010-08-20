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
// File: PLoginProcess.php
//
// Description: Verify user and password. Create a session and store userinfo in.
// Support several ways of signing in, depends on the input.
//
// Author: Mikael Roos, mos@bth.se
//
// Known issues:
// -
//
// History: 
// 2010-06-23: New structure for instantiating controllers. Included license message.
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
$submit				= $pc->POSTisSetOrSetDefault('submit');
$account			= $pc->POSTisSetOrSetDefault('account');
$password 		= $pc->POSTisSetOrSetDefault('password');
$redirect			= $pc->POSTisSetOrSetDefault('redirect');
$redirectFail	= $pc->POSTisSetOrSetDefault('redirect-fail');
$silent				= $pc->SESSIONIsSetOrSetDefault('silentLoginAccount');


// -------------------------------------------------------------------------------------------
//
// Silent login.
// Check if this is a silent login attempt where another pagecontroller is initiating the 
// login process. For example when creating a new account and login simoultaneously.
// Get the login info from the session instead from the POST.
//
if(!empty($silent)) {

	$submit = 'login-local';
	$account 			= $pc->SESSIONIsSetOrSetDefault('silentLoginAccount');
	$password 		= $pc->SESSIONIsSetOrSetDefault('silentLoginPassword');
	$redirect 		= $pc->SESSIONIsSetOrSetDefault('silentLoginRedirect');
	$redirectFail = $pc->SESSIONIsSetOrSetDefault('silentLoginRedirectFail');

	unset($_SESSION['silentLoginAccount']);
	unset($_SESSION['silentLoginPassword']);
	unset($_SESSION['silentLoginRedirect']);
	unset($_SESSION['silentLoginRedirectFail']);
}


// -------------------------------------------------------------------------------------------
//
// Account nor password can be empty.
// Set error message and redirect to fail.
//
if(empty($account) || empty($password)) {
	$pc->SetSessionMessage('loginFailed', $pc->lang['BOTH_MUST_HAVE_VALUES']);
	$pc->SetSessionMessage('loginAccount', $account);
	$pc->RedirectTo($redirectFail);
}


// -------------------------------------------------------------------------------------------
//
// Get database connection
//
$mysqli = $db->Connect();


// -------------------------------------------------------------------------------------------
//
// Authenticate, do authentication based on the submit action.
//
// Should start by doing some insane checking to avoid cracking. 
// For example to allow only 5 login attempt from same ip-adress within a period of time.
// 
if(false) { 
	; 
}


// -------------------------------------------------------------------------------------------
//
// Authenticate using local user in database
//
else if($submit == 'login-local') {

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
	$accountId='';
	require(TP_MODULESPATH . '/core/login/ILoginDatabaseProcess.php');
}


// -------------------------------------------------------------------------------------------
//
// Authenticate using LDAP
//
else if($submit == 'login-ldap') {

	// ===========================================================================================
	//
	// File: ILoginLDAPProcess.php
	//
	// Description: Submit-action to authenticate user from a LDAP-server. 
	// To be included in a pagecontroller that has the correct environment set.
	//
	// Author: Mikael Roos, mos@bth.se
	//
	// Preconditions:
	//
	// Variables must be defined by pagecontroller:
	// $account 
	// $password
	// $mail (OUT, sets the mailadress if it exists)
	// $redirectFail
	//
	// Require from pagecontroller using:
	// require(TP_MODULESPATH . '/core/login/ILoginLDAPProcess.php');
	//
	// Messages that may be set in session reflecting the outcome of the action:
	// loginFailed
	// loginAccount
	//
	require(TP_MODULESPATH . '/core/login/ILoginLDAPProcess.php');
	
	//
	// The account is authenticated in the LDAP.
	// But it also must exists in the local database, if it does, then login.
	// If not, then we need to create a new account and login.
	//
	// We could also use some profile values from the LDAP server.
	// These should then be checked each time the user performs a login.
	//
	$query = <<< EOD
CALL {$db->_['PGetOrCreateAccountId']}(@accountId, '{$account}', '{$mail}');
SELECT
	@accountId AS accountId;
EOD;
	
	// Perform the query
	$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);
	
	// Get account details 	
	$row = $results[1]->fetch_object();
	$accountId = $row->accountId;
	$results[1]->close(); 
}


// -------------------------------------------------------------------------------------------
//
// User is authenticated and exists in the local database.
// Prepare to populate session by getting details about this account 
// from the user profile in the database.
//
$query = <<< EOD
CALL {$db->_['PGetAccountDetails']}({$accountId});
EOD;

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);
	
// Get account details 	
$row = $results[0]->fetch_object();
$account 				= $row->account;
$gravatarmicro	= $row->gravatarmicro;
$results[0]->close(); 

// Get group memberships details 	
$groups = Array();
while($row = $results[1]->fetch_object()) {    
	$groups[$row->groupid] = $row->groupname;
}
$results[1]->close(); 

$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Finally: Populate the session
//
// Destroy the current session (logout user), if it exists. 
// Remember where we are going, this enables us to redirect to the initial pagerequest,
// even after several unsuccessfull login attempts.
//

// Destroy current session and restart it
require_once(dirname(__FILE__) . '/IDestroySession.php');

// Populate the session with the user (object)
$uc = CUserController::GetInstance();
$uc->Populate($accountId, $account, $groups, (empty($gravatarmicro) ? '' : $gravatarmicro));
$uc->StoreInSession();

$pc->SetSessionMessage('userFeedbackNotice', sprintf($pc->lang['WELCOME'], $uc->GetAccountName()));


// -------------------------------------------------------------------------------------------
//
// Redirect to another page
//
$pc->RedirectTo($redirect);


?>