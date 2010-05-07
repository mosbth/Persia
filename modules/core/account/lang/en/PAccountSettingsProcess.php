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
	'PASSWORD_DOESNT_MATCH' => "The passwords does not match.",
	'PASSWORD_CANNOT_BE_EMPTY' => "The password was empty, a password must not be empty.",
	'PASSWORD_WAS_NOT_UPDATED' => "The password was not updated in the database.",
	'CHANGE_PASSWORD_SUCCESS' => "The password was changed.",
	'EMAIL_WAS_NOT_UPDATED' => "The email was not updated in the database.", 
	'AVATAR_WAS_NOT_UPDATED' => "The avatar was not updated in the database.",
	'SUBMIT_ACTION_NOT_SUPPORTED' => "The submit is not supported. Failing.",
	'MISMATCH_SESSION_AND_SETTINGS' => "You are trying to change an account but you are actually signed in on another account. Failing.",
	'SUCCESSFULLY_SENT_MAIL' => "Successfully sent mail to '%s'.",
	'FAILED_SENDING_MAIL' => "Failed to send mail to '%s'. Perhaps malformed mailadress?",

	// Change email confirmation mail
	'MAIL_NEW_MAILADRESS_CONFIRMATION_SUBJECT' => "[Persia] New mailadress saved in the profile",
	'MAIL_NEW_MAILADRESS_CONFIRMATION_BODY' => 
		"Hi," .
		"\n" .
		"This is a confirmation mail since you appear to have changed the mailadress in your profile." .
		"\n\n" .
		"This will now be the mailadress we use to send you information and messages from the system. " . 
		"This mailadress may also be used for assistance if you forget your password. " . 
		"So, it's important that this mailadress is correct." .
		"\n\n" .
		"Best regards,\n" .
		"The Development Team Of Persia\n" .
		"http://phpersia.org\n",


);

?>