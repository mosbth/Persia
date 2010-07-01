<?php
// ===========================================================================================
//
// File: PAccountForgotPasswordProcess.php
//
// Description: Language file
//
// Author: Mikael Roos, mos@bth.se
//

$lang = Array(

	// Step 1 
	'CAPTCHA_FAILED' => "CAPTCHA check failed: The magic word did not match, please try again.",
	'NO_MATCH1' => "There is no account with such name nor such mailadress.",
	'NO_MAIL_CONNECTED' => "There is no mail connected with this account. This service needs an mail to be connected to the account. Otherwise it can not assist.",
	'SUBMIT_ACTION_NOT_SUPPORTED' => "The action is not supported by this page. Report this as an error.",

	// Mail
	'SUCCESSFULLY_SENT_MAIL' => "Successfully sent mail to '%s'.",
	'FAILED_SENDING_MAIL' => "Failed to send mail to '%s'. Perhaps malformed mailadress?",

	// Change email confirmation mail
	'MAIL_LOST_PASSWORD_SUBJECT' => "".WS_MAILSUBJECTLABEL."Have you lost your password?",
	'MAIL_LOST_PASSWORD_BODY' => 
		"Hi," .
		"\n" .
		"It seems like you have asked for help with resetting your password. " . 
		"You can safely ignore this mail if that is not correct." . 
		"\n\n" .
		"The key below is needed to reset your password. Copy and paste it into the webform. " . 
		"The key is active for one hour. After that you will need to redo the procedure again. The key follows: " . 
		"\n\n" .
		"%s" . 
		WS_MAILSIGNATURE,


	// Step 2
	'NO_MATCH2' => "The key did not match. Try to enter it again. Try to redo the process if it fails again.",
	'KEY_TIME_EXPIRED' => "The time has expired for the key. You have to redo the process from the beginning.",
	'SUCCESSFULLY_VERIFIED_KEY' => "The key was successfully verified.",
	'SESSION_KEY_LOST' => "The matching key in the session was lost. Please redo the process from the beginning.",

	// Step 3
	'NO_MATCH3' => "The key did not match. Try to enter it again. Try to redo the process if it fails again.",


);

?>