<?php
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


// -------------------------------------------------------------------------------------------
//
// General settings for this file.
//
$pc->LoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// Perform authentication using LDAP-server.
//

// Set up the environment
$server 	= LDAP_AUTH_SERVER;
$basedn 	= LDAP_AUTH_BASEDN;

// Create LDAP objecy and Connect
$ldap = new CLDAP($server);
$ds = $ldap->ConnectAndSetOptions();

// Should check that its connected but returnvalue $ds varies

// Authenticate user and password
$res = $ldap->Authenticate($ds, $basedn, $account, $password);

if(!$res) {
	$pc->SetSessionMessage('loginFailed', $pc->lang['AUTHENTICATION_FAILED']);
	$pc->SetSessionMessage('loginAccount', $account);
	$pc->RedirectTo($redirectFail);	
}

// Clean up
ldap_close($ds);


?>