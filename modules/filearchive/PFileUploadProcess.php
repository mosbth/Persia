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
$userId		= $_SESSION['idUser'];

// All files are stored in the users own directory
$account 			= $pc->SESSIONisSetOrSetDefault('accountUser');
$archivePath 	= $pc->AddTrailingSeparatorIfNeeded(FILE_ARCHIVE_PATH) . $account . DIRECTORY_SEPARATOR;
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
// Upload single file and return html success/failure message. Ajax-like.
// 
else if($submitAction == 'upload-return-html') {

	// http://www.php.net/manual/en/features.file-upload.errors.php
	$errorMessages = Array (
		UPLOAD_ERR_INI_SIZE 	=> $pc->lang['UPLOAD_ERR_INI_SIZE'],
		UPLOAD_ERR_FORM_SIZE 	=> $pc->lang['UPLOAD_ERR_FORM_SIZE'],
		UPLOAD_ERR_PARTIAL 		=> $pc->lang['UPLOAD_ERR_PARTIAL'],
		UPLOAD_ERR_NO_FILE 		=> $pc->lang['UPLOAD_ERR_NO_FILE'],
		UPLOAD_ERR_NO_TMP_DIR => $pc->lang['UPLOAD_ERR_NO_TMP_DIR'],
		UPLOAD_ERR_CANT_WRITE => $pc->lang['UPLOAD_ERR_CANT_WRITE'],
		UPLOAD_ERR_EXTENSION 	=> $pc->lang['UPLOAD_ERR_EXTENSION'],		
	);
	
	// Create a unique filename
	do {
		$file = uniqid();
		$path = $archivePath . $file;
	} while(file_exists($path));

	// Move the uploaded file
	$html = '';
	if (move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
		$html = CHTMLHelpers::GetHTMLUserFeedbackPositive(sprintf($pc->lang['FILE_UPLOAD_SUCCESS'], $_FILES['file']['name'], $_FILES['file']['size'], $_FILES['file']['type']));
	} else {
		$html = CHTMLHelpers::GetHTMLUserFeedbackNegative(sprintf($pc->lang['FILE_UPLOAD_FAILED'], $_FILES['file']['error'], $errorMessages[$_FILES['file']['error']]));
	}

	//
	// Store metadata of the file in the database
	//
	$db 		= new CDatabaseController();
	$mysqli = $db->Connect();

	// Create the query
	$query 	= <<< EOD
CALL {$db->_['PInsertFile']}('{$userId}', '{$_FILES['file']['name']}', '{$file}', '{$path}', {$_FILES['file']['size']}, '{$_FILES['file']['type']}');
EOD;

	// Perform the query
	$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

	// Assume it all whent okey

	$mysqli->close();

	// Echo out the result
	echo $html;
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
	
	echo 'The file is not stored in the database, just uploaded. Here is some more debugging info:';
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
	
	echo 'The file is not stored in the database, just uploaded. Here is some more debugging info:';
	print_r($_FILES);
	
	print "</pre>";
	exit;
}


// -------------------------------------------------------------------------------------------
//
// Default, submit-action not supported, show error and die.
// 
die($pc->lang['SUBMIT_ACTION_NOT_SUPPORTED']);


?>