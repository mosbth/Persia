<?php
// ===========================================================================================
//
// File: PLoginProcess.php
//
// Description: Verify user and password. Create a session and store userinfo in.
// Support several ways of signing in, depends on the input.
//
//
//
// Author: Mikael Roos, mos@bth.se
//
// History:
// 2010-05-09: Added support for LDAP
// 2010-05-01: Added support for silent login where anothe pagecontroller can initiate a login.
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


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
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
$db = new CDatabaseController();
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
	// $pc
	// $db
	// $mysqli
	// $account 
	// $accountId (OUT, id of the account if successful) 
	// $password
	// $redirectFail
	//
	// Include from pagecontroller using:
	// include(dirname(__FILE__) . '/IAccountChangePasswordProcess.php');
	//
	// Messages that may be set in session reflecting the outcome of the action:
	// loginFailed
	// loginAccount
	//
	$accountId='';
	include(dirname(__FILE__) . '/ILoginDatabaseProcess.php');
}


// -------------------------------------------------------------------------------------------
//
// Authenticate using LDAP
//
else if($submit == 'login-ldap') {

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
	// $pc
	// $account 
	// $password
	// $redirectFail
	//
	// Include from pagecontroller using:
	// include(dirname(__FILE__) . '/ILoginLDAPProcess.php');
	//
	// Messages that may be set in session reflecting the outcome of the action:
	// loginFailed
	// loginAccount
	//
	include(dirname(__FILE__) . '/ILoginLDAPProcess.php');

	//
	// The account is authenticated in the LDAP.
	// But it also must exists in the local database, if it does, then login.
	// If not, then we need to create a new account and login.
	//
	// We could also use some profile values from the LDAP server.
	// These should then be checked each time the user performs a login.
	//
	$query = <<< EOD
CALL {$db->_['PGetOrCreateAccountId']}(@accountId, {$account});
SELECT
	@accountId AS accountId;
EOD;
	
	// Perform the query
	$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);
	
	// Get account details 	
	$row = $results[0]->fetch_object();
	$accountId = $row->accountId;
	$results[0]->close(); 
}


// -------------------------------------------------------------------------------------------
//
// User is authenticatedand exists in the local database.
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
$name						= $row->name;
$email					= $row->email;
$avatar 				= $row->avatar;
$gravatar 			= $row->gravatar;
$gravatarmicro	= $row->gravatarmicro;
$gravatarsmall	= $row->gravatarsmall;
$groupakronym		= $row->groupakronym;
$groupdesc			= $row->groupdesc;
$results[0]->close(); 

$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Finally: Populate the session
//
// Destroy the current session (logout user), if it exists. 
// Remember where we are going, this enables us to redirect to the initial pagerequest,
// even after several unsuccessfull login attempts.
//

// Destroy current session
require_once(TP_SOURCEPATH . 'FDestroySession.php');

// Start a named session
session_name(preg_replace('/[:\.\/-_]/', '', WS_SITELINK));
session_start();
session_regenerate_id(); 	// To avoid problems 

// Populate the session with the user (object)
$_SESSION['idUser'] 						= $accountId;
$_SESSION['gravatarUserMicro'] 	= empty($gravatarmicro) ? '' : $gravatarmicro;		
$_SESSION['accountUser'] 				= $account;		
$_SESSION['groupMemberUser'] 		= $groupakronym;		


// -------------------------------------------------------------------------------------------
//
// Redirect to another page
//
$pc->RedirectTo($redirect);

?>