<?php
// ===========================================================================================
//
// File: PAccountSettingsProcess.php
//
// Description: Language file
//
// Author: Mikael Roos, mos@bth.se
//

$lang = Array(
	// change-password
	'PASSWORD_DOESNT_MATCH' => "The passwords does not match.",
	'PASSWORD_CANNOT_BE_EMPTY' => "The password was empty, a password must not be empty.",
	'CHANGE_PASSWORD_SUCCESS' => "The password was changed.",

	//
	'SUBMIT_ACTION_NOT_SUPPORTED' => "The submit is not supported. Failing.",
	'MISMATCH_SESSION_AND_SETTINGS' => "You are trying to change an account but you are actually signed in on another account. Failing.",
	'SUCCESSFULLY_SENT_MAIL' => "Successfully sent mail to '%s'.",
	'FAILED_SENDING_MAIL' => "Failed to send mail to '%s'. Perhaps malformed mailadress?",

	// Change email confirmation mail
	'MAIL_NEW_MAILADRESS_CONFIRMATION_SUBJECT' => "".WS_MAILSUBJECTLABEL."New mailadress saved in the profile",
	'MAIL_NEW_MAILADRESS_CONFIRMATION_BODY' => 
		"Hi," .
		"\n" .
		"This is a confirmation mail since you appear to have changed the mailadress in your profile." .
		"\n\n" .
		"This will now be the mailadress we use to send you information and messages from the system. " . 
		"This mailadress may also be used for assistance if you forget your password. " . 
		"So, it's important that this mailadress is correct." .
		WS_MAILSIGNATURE,


);

?>