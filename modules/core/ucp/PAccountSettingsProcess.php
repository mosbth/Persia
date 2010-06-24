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
// File: PAccountSettingsProcess.php
//
// Description: Save changes in profile and account settings.
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
$if->UserIsSignedInOrRedirectToSignIn();
$if->UserIsCurrentUserOrMemberOfGroupAdminOr403($uc->GetAccountId());


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
// Always check whats coming in...
//
$userId	= $uc->GetAccountId();
$submitAction	= $pc->POSTisSetOrSetDefault('submit');
$accountId		= $pc->POSTisSetOrSetDefault('accountid');
$redirect			= $pc->POSTisSetOrSetDefault('redirect');
$redirectFail	= $pc->POSTisSetOrSetDefault('redirect-failure');


// -------------------------------------------------------------------------------------------
//
// Depending on the submit-action, do whats to be done. If, else if, else, replaces switch.
// 

// -------------------------------------------------------------------------------------------
//
// Do some insane checking to avoid misusage, errormessage if not correct.
// 
// Are we trying to change the same user profile as is signed in? Must be Yes.
// Might change to allow admin to change profile (or maybe not)
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

	// 
	// IAccountChangePasswordProcess
	// 
	// Preconditions:
	//
	// Variables must be defined by pagecontroller:
	// $pc
	// $userId
	// $password1
	// $password2
	// $redirectFail
	//
	// Include from pagecontroller using:
	// include(dirname(__FILE__) . '/IAccountChangePasswordProcess.php');
	//
	// Messages that may be set in session reflecting the outcome of the action:
	// changePwdFailed
	// changePwdSuccess
	//
	include(dirname(__FILE__) . '/../account/IAccountChangePasswordProcess.php');


	// Redirect to resultpage
	$pc->RedirectTo($redirect);
}


// -------------------------------------------------------------------------------------------
//
// Change mail
// 
else if($submitAction == 'change-mail') {

	// Get and prepare all variables
	$mysqli 			= $db->Connect();
	$mailAddress	= $pc->POSTisSetOrSetDefault('mail');
	$mailAddress1 = $mysqli->real_escape_string($mailAddress);

	// Create and perform db query
	$query = <<<EOD
CALL {$db->_['PChangeAccountEmail']}('{$userId}', '{$mailAddress1}', @rowcount);
SELECT @rowcount AS rowcount;
EOD;

	$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

	// Take care of results from db query
	$row = $results[1]->fetch_object();
	if($row->rowcount == 1) {
	
		// Send a mail to the new mailadress
		$mail = new CMail();
		$r = $mail->SendMail($mailAddress, $pc->lang['MAIL_NEW_MAILADRESS_CONFIRMATION_SUBJECT'], $pc->lang['MAIL_NEW_MAILADRESS_CONFIRMATION_BODY']);

		if($r) {
			$pc->SetSessionMessage('mailSuccess', sprintf($pc->lang['SUCCESSFULLY_SENT_MAIL'], $mailAddress));
		}else {
			$pc->SetSessionMessage('mailFailed', sprintf($pc->lang['FAILED_SENDING_MAIL'], $mailAddress));
		}
	
	}
	$mysqli->close();
	
	// Redirect to resultpage
	$pc->RedirectTo($redirect);
}


// -------------------------------------------------------------------------------------------
//
// Change avatar
// 
else if($submitAction == 'change-avatar') {

	// Get and prepare all variables
	$mysqli = $db->Connect();
	$avatar = $mysqli->real_escape_string($pc->POSTisSetOrSetDefault('avatar'));

	// Prepare query
	$query = "CALL {$db->_['PChangeAccountAvatar']}('{$userId}', '{$avatar}');";

	// Perform the query, ignore the results
	$db->DoMultiQueryRetrieveAndStoreResultset($query);

	$mysqli->close();

	// Redirect to resultpage
	$pc->RedirectTo($redirect);
}


// -------------------------------------------------------------------------------------------
//
// Change gravatar
// 
else if($submitAction == 'change-gravatar') {

	// Get and prepare all variables
	$mysqli = $db->Connect();
	$gravatar = $mysqli->real_escape_string($pc->POSTisSetOrSetDefault('gravatar'));

	// Perform the query, ignore the results
	$query = "CALL {$db->_['PChangeAccountGravatar']}('{$userId}', '{$gravatar}');";
	$db->DoMultiQueryRetrieveAndStoreResultset($query);

	// Get the updated gravatar and store in the session
	$query = "CALL {$db->_['PGetAccountDetails']}({$accountId});";
	$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);
	
	// Get account details 	
	$row = $results[0]->fetch_object();
	$gravatarmicro	= $row->gravatarmicro;
	$results[0]->close(); 
	$mysqli->close();
	
	// Update the gravatar in the session
	$uc->Update('gravatar', (empty($gravatarmicro) ? '' : $gravatarmicro));
	$uc->StoreInSession();
	
	// Redirect to resultpage
	$pc->RedirectTo($redirect);
}


// -------------------------------------------------------------------------------------------
//
// Default, submit-action not supported, show error and die.
// 
die($pc->lang['SUBMIT_ACTION_NOT_SUPPORTED']);


?>