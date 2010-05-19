<?php
// ===========================================================================================
//
// File: PFileUploadProcess.php
//
// Description: Upload and store files in the users file archive.
//
// Author: Mikael Roos, mos@bth.se
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
$intFilter->UserIsSignedInOrRecirectToSignIn();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$submitAction	= $pc->POSTisSetOrSetDefault('do-submit');
$redirect			= $pc->POSTisSetOrSetDefault('redirect');
$redirectFail	= $pc->POSTisSetOrSetDefault('redirect-fail');

$account = $pc->SESSIONisSetOrSetDefault('accountUser');
$archivePath = FILE_ARCHIVE_PATH . DIRECTORY_SEPARATOR . $account . DIRECTORY_SEPARATOR;
if(!is_dir($archivePath)) {
	mkdir($archivePath);
}

// Always check whats coming in...
//$pc->IsNumericOrDie($articleId, 0);



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
// Upload multiple files by a traditional form
// 
else if($submitAction == 'ajax-enabled') {

	if (move_uploaded_file($_FILES['file']['tmp_name'], $archivePath . basename($_FILES['file']['name']))) {
		$success = 1;
	} else {
		$success = 0;
	}

//
// Replace the following with the PHP JSON extension when knowing we have PHP 5.2.0 or higher.
// http://www.php.net/manual/en/book.json.php
//
	$json = <<<EOD
{
	"success": {$success},
	"name": "{$_FILES['file']['name']}",
	"type": "{$_FILES['file']['type']}",
	"size": {$_FILES['file']['size']},
	"error": {$_FILES['file']['error']},
}
EOD;

	echo $json;
	exit;
}


// -------------------------------------------------------------------------------------------
//
// Upload a single file by a traditional form
// 
else if($submitAction == 'single-by-traditional-form') {

	echo '<pre>';
	if (move_uploaded_file($_FILES['file']['tmp_name'], $archivePath . basename($_FILES['file']['name']))) {
			echo "File is valid, and was successfully uploaded.\n";
	} else {
			echo "Possible file upload attack!\n";
	}
	
	echo 'Here is some more debugging info:';
	print_r($_FILES);
	
	print "</pre>";
	exit;
}


// -------------------------------------------------------------------------------------------
//
// Upload multiple files by a traditional form
// 
else if($submitAction == 'multiple-by-traditional-form') {

	echo '<pre>';
	foreach ($_FILES["file"]["error"] as $key => $error) {
		if ($error == UPLOAD_ERR_OK) {
			echo "File '{$key}' is valid, and was successfully uploaded.\n";
			move_uploaded_file($_FILES["file"]["tmp_name"][$key], $archivePath . basename($_FILES["file"]["name"][$key]));
		} else {
			echo "Possible file upload attack!\n";		
		}
	}
	
	echo 'Here is some more debugging info:';
	print_r($_FILES);
	
	print "</pre>";
	exit;
}




/*
// Get the input and check it
	$account		= $pc->POSTisSetOrSetDefault('account');
	$password1	= $pc->POSTisSetOrSetDefault('password1');
	$password2	= $pc->POSTisSetOrSetDefault('password2');

	$_SESSION['account'] = $account;
	//
	// Check the characters in the username
	//
	trim($account);
	if(preg_replace('/[a-zA-Z0-9]/', '', $account)) {
		$pc->SetSessionMessage('createAccountFailed', $pc->lang['INVALID_ACCOUNT_NAME']);
		$pc->RedirectTo($redirectFail);		
	}

	//
	// Check the passwords
	//
	if(empty($password1) || empty($password2)) {
		$pc->SetSessionMessage('createAccountFailed', $pc->lang['PASSWORD_CANNOT_BE_EMPTY']);
		$pc->RedirectTo($redirectFail);
	} 
	else if($password1 != $password2) {
		$pc->SetSessionMessage('createAccountFailed', $pc->lang['PASSWORD_DOESNT_MATCH']);
		$pc->RedirectTo($redirectFail);
	}

	//
	// Check the CAPTCHA
	//
	$captcha = new CCaptcha();
	if(!$captcha->CheckAnswer()) {
		$pc->SetSessionMessage('createAccountFailed', $pc->lang['CAPTCHA_FAILED']);
		$pc->RedirectTo($redirectFail);		
	}

	//
	// Execute the database query to make the update
	//
	$db = new CDatabaseController();
	$mysqli = $db->Connect();

	// Prepare query
	$account 	= $mysqli->real_escape_string($account);
	$password = $mysqli->real_escape_string($password1);
	$hashingalgoritm = DB_PASSWORDHASHING;

	$query = <<<EOD
CALL {$db->_['PCreateAccount']}(@accountId, '{$account}', '{$password}', '{$hashingalgoritm}', @status);
SELECT 
	@accountId AS accountid,
	@status AS status;
EOD;

	// Perform the query
	$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

	// Get details from resultset
	$row = $results[1]->fetch_object();

	if($row->status == 1) {
		$pc->SetSessionMessage('createAccountFailed', $pc->lang['ACCOUNTNAME_ALREADY_EXISTS']);
		$pc->RedirectTo($redirectFail);	
	}
	
	$results[1]->close();
	$mysqli->close();

	//
	// Do a silent login and then proceed to $redirect
	//
	unset($_SESSION['account']);
	$_SESSION['silentLoginAccount'] 	= $account;
	$_SESSION['silentLoginPassword'] 	= $password;
	$_SESSION['silentLoginRedirect'] 	= $redirect;
	$pc->RedirectTo($silentLogin);
}
*/

// -------------------------------------------------------------------------------------------
//
// Default, submit-action not supported, show error and die.
// 
die($pc->lang['SUBMIT_ACTION_NOT_SUPPORTED']);


?>