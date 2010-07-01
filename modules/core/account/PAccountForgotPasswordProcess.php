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
// File: PAccountForgotPassword1Process.php
//
// Description: Aid when forgetting passwords, sends email to the accountowner,
// using the email related to the account.
//
// Author: Mikael Roos, mos@bth.se
//
// Known issues:
// -
//
// History: 
// 2010-07-01: New structure for instantiating controllers. Included license message.
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
$submitAction	= $pc->POSTisSetOrSetDefault('submit');
$redirect			= $pc->POSTisSetOrSetDefault('redirect');
$redirectFail	= $pc->POSTisSetOrSetDefault('redirect-fail');
$silentLogin	= $pc->POSTisSetOrSetDefault('silent-login');


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
// STEP 1: Find the mail adress and send a rescue mail
//
// Define a custom filter that is needed to proceed:
//  $if->CustomFilterIsSetOrDie('resetPassword', 'set');
// 
else if($submitAction == 'send-rescue-mail') {

	// Get the input and check it
	$account = $pc->POSTisSetOrSetDefault('account');
	$_SESSION['account'] = $account;

	//
	// Check the CAPTCHA
	//
	$captcha = new CCaptcha();
	if(!$captcha->CheckAnswer()) {
		$pc->SetSessionMessage('forgotPwdFailed', $pc->lang['CAPTCHA_FAILED']);
		$pc->RedirectTo($redirectFail);		
	}
	
	//
	// Execute the database query to find mailadress
	//
	$mysqli = $db->Connect();

	// Prepare query
	$account 	= $mysqli->real_escape_string($account);
	$query = <<<EOD
CALL {$db->_['PGetMailAdressFromAccount']}('{$account}', @aAccount, @aMail, @aStatus);
SELECT 
	@aAccount AS account,
	@aMail AS mail,
	@aStatus AS status;
EOD;

	// Perform the query & get results
	$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);
	$row = $results[1]->fetch_object();

	//
	// Did something fail?
	//
	switch($row->status) {
		case 1: {
			$pc->SetSessionMessage('forgotPwdFailed', $pc->lang['NO_MAIL_CONNECTED']);
			$pc->RedirectTo($redirectFail);	
		} break;
		
		case 2: {
			$pc->SetSessionMessage('forgotPwdFailed', $pc->lang['NO_MATCH1']);
			$pc->RedirectTo($redirectFail);	
		} break;
	}
	$mailadress = $row->mail;
	
	//
	// Initiate to re-set password, store key1 in the session and the get the key2 to send to the user
	// The resulting key3 is stored in the database.
	//
	$_SESSION['key1'] = md5(uniqid());
	$query = <<<EOD
SET @aKey = '{$_SESSION['key1']}';
CALL {$db->_['PPasswordResetGetKey']}('{$row->account}', @aKey);
SELECT @aKey AS key2;
EOD;
 
 	// Perform the query
	$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

	// Get details from resultset
	$row = $results[2]->fetch_object();

	//
	// Send a mail to the mailadress with the key2
	//
	$mail = new CMail();
	$r = $mail->SendMail(	$mailadress, $pc->lang['MAIL_LOST_PASSWORD_SUBJECT'], sprintf($pc->lang['MAIL_LOST_PASSWORD_BODY'], $row->key2));
	if(!$r) {
		$pc->SetSessionMessage('mailFailed', sprintf($pc->lang['FAILED_SENDING_MAIL'], $mailadress));
		$pc->RedirectTo($redirectFail);	
	}
		
	$results[2]->close();
	$mysqli->close();
	unset($_SESSION['account']);
	
	// Enable custom filter
	$if->CustomFilterIsSetOrDie('resetPassword', 'set');

	$pc->SetSessionMessage('mailSuccess', sprintf($pc->lang['SUCCESSFULLY_SENT_MAIL'], $mailadress));
	$pc->RedirectTo($redirect);	
}


// -------------------------------------------------------------------------------------------
//
// STEP 2: Take the key from the mail and verify it
//
// Check that the custom filter is set:
//  $if->CustomFilterIsSetOrDie('resetPassword', 'set');
//  $if->CustomFilterIsSetOrDie('resetPassword');
// 
else if($submitAction == 'verify-key') {

	// Check that customfilter for this process is defined, to avoid bots with direct links
	$if->CustomFilterIsSetOrDie('resetPassword');

	// Get the input and check it
	$key2 = trim(strip_tags($pc->POSTisSetOrSetDefault('key2')));
	$_SESSION['key2'] = $key2;

	//
	// Check key1 from the session
	//
	$key1 = $pc->SESSIONisSetOrSetDefault('key1', '');
	if(empty($key1)) {
		$pc->SetSessionMessage('forgotPwdFailed', $pc->lang['SESSION_KEY_LOST']);
		$pc->RedirectTo($redirectFail);		
	}

	//
	// Check the CAPTCHA
	//
	$captcha = new CCaptcha();
	if(!$captcha->CheckAnswer()) {
		$pc->SetSessionMessage('forgotPwdFailed', $pc->lang['CAPTCHA_FAILED']);
		$pc->RedirectTo($redirectFail);		
	}
	
	//
	// Execute the database query to verify the key
	//
	$mysqli = $db->Connect();

	// Prepare query
	$key2	= $mysqli->real_escape_string($key2);
	
	$query = <<<EOD
CALL {$db->_['PPasswordResetActivate']}(@aAccountId, @aAccountName, '{$key1}', '{$key2}', @aStatus);
SELECT 
	@aAccountName AS accountName,
	@aAccountId AS accountId,
	@aStatus AS status;
EOD;

	// Perform the query and get results
	$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);
	$row = $results[1]->fetch_object();

	//
	// Did something fail?
	//
	switch($row->status) {
		case 1: {
			$pc->SetSessionMessage('forgotPwdFailed', $pc->lang['KEY_TIME_EXPIRED']);
			$pc->RedirectTo($redirectFail);	
		} break;
		
		case 2: {
			$pc->SetSessionMessage('forgotPwdFailed', $pc->lang['NO_MATCH2']);
			$pc->RedirectTo($redirectFail);	
		} break;
	}
	
	$_SESSION['accountName']	= $row->accountName;
	$_SESSION['accountId'] 		= $row->accountId;
	unset($_SESSION['key1']);
	unset($_SESSION['key2']);
	$mysqli->close();

	$pc->SetSessionMessage('keySuccess', $pc->lang['SUCCESSFULLY_VERIFIED_KEY']);
	$pc->RedirectTo($redirect);	
}


// -------------------------------------------------------------------------------------------
//
// STEP 3: Change password and do silent login
//
// Check that the custom filter is set:
//  $if->CustomFilterIsSetOrDie('resetPassword', 'set');
//  $if->CustomFilterIsSetOrDie('resetPassword');
// 
else if($submitAction == 'change-password') {

	// Check that customfilter for this process is defined, to avoid bots with direct links
	$if->CustomFilterIsSetOrDie('resetPassword');

	// Change password and do silent login
	$userId = $pc->SESSIONisSetOrSetDefault('accountId');
	// 
	// IAccountChangePasswordProcess
	// 
	// Preconditions:
	//
	// Variables must be defined by pagecontroller:
	// $userId
	// $password1
	// $password2
	// $redirectFail
	//
	// Include from pagecontroller using:
	// require(dirname(__FILE__) . '/IAccountChangePasswordProcess.php');
	//
	// Messages that may be set in session reflecting the outcome of the action:
	// changePwdFailed
	// changePwdSuccess
	//
	require(dirname(__FILE__) . '/IAccountChangePasswordProcess.php');

	// Use the account and the password to do a silent login	
	$_SESSION['silentLoginAccount'] 	= $pc->SESSIONisSetOrSetDefault('accountName');
	$_SESSION['silentLoginPassword'] 	= $password1;
	$_SESSION['silentLoginRedirect'] 	= $redirect;
	$pc->RedirectTo($silentLogin);
}


// -------------------------------------------------------------------------------------------
//
// Default, submit-action not supported, show error and die.
// 
die($pc->lang['SUBMIT_ACTION_NOT_SUPPORTED']);


?>