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
// File: ILoginLDAPProcess.php
//
// Description: Submit-action to authenticate user from a LDAP-server. 
// To be included in a pagecontroller that has the correct environment set.
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
// Author: Mikael Roos, mos@bth.se
//
// Known issues:
// Test with TSL and LDAPS.
//
// History: 
// 2010-06-23: Updated to work with Dada.
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
$res = $ldap->Authenticate($ds, $basedn, $account, $password, $mail);

if(!$res) {
	$pc->SetSessionMessage('loginFailed', $pc->lang['AUTHENTICATION_FAILED']);
	$pc->SetSessionMessage('loginAccount', $account);
	$pc->RedirectTo($redirectFail);	
}

// Clean up
ldap_close($ds);


?>