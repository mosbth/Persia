<?php
// ===========================================================================================
//
// File: PFileDownloadProcess.php
//
// Description: Initiate actual download of file.
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


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$file	= $pc->GETisSetOrSetDefault('file');


// -------------------------------------------------------------------------------------------
//
// Get file details/metadata from database
//
$db 		= new CDatabaseController();
$mysqli = $db->Connect();

// Create the query
$file 	= $mysqli->real_escape_string($file);
$query 	= <<< EOD
CALL {$db->_['PFileDetails']}(NULL, '{$file}', @success);
SELECT @success AS success;
EOD;

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

$row = $results[2]->fetch_object();

// If file is not valid then redirect to 403 with a message
if($row->success > 1) {
	$pc->RedirectToModuleAndPage('', 'p403', '', $db->_['FFileCheckPermissionMessages'][$row->success]);
}

$row = $results[0]->fetch_object();
$name 			= $row->name;
$path 			= $row->path;
$size 			= $row->size;
$mimetype 	= $row->mimetype;
$created 		= $row->created;
$modified 	= $row->modified;

$results[2]->close();
$results[0]->close();
$mysqli->close();

// The file must exist, else redirect to 404
if(!is_readable($path)) {
	$pc->RedirectToModuleAndPage('', 'p404', '', $pc->lang['FILE_DOES_NOT_EXISTS_ON_DISK']);
}


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
header("Content-type: {$mimetype}");
header("Content-Disposition: attachment; filename=\"{$name}\"");
readfile($path);
exit;

?>