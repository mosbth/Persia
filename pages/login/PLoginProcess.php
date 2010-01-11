<?php
// ===========================================================================================
//
// PLoginProcess.php
//
// Verify user and password. Create a session and store userinfo in.
//


// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
require_once(TP_SOURCEPATH . 'CPagecontroller.php');

$pc = new CPagecontroller();


// -------------------------------------------------------------------------------------------
//
// Interception Filter, access, authorithy and other checks.
//
require_once(TP_SOURCEPATH . 'CInterceptionFilter.php');

$intFilter = new CInterceptionFilter();

$intFilter->frontcontrollerIsVisitedOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of global pageController settings, can exist for several pagecontrollers.
// Decide how page is displayed, review CHTMLPage for supported types.
//
$displayAs = $pc->GETisSetOrSetDefault('pc_display', '');


// -------------------------------------------------------------------------------------------
//
// Page specific code
//


// -------------------------------------------------------------------------------------------
//
// Destroy the current session (logout user), if it exists. 
//
require_once(TP_SOURCEPATH . 'FDestroySession.php');


// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database.
//
$mysqli = $pc->ConnectToDatabase();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$user 		= isset($_POST['nameUser']) 	? $_POST['nameUser'] 		: '';
$password 	= isset($_POST['passwordUser']) ? $_POST['passwordUser'] 	: '';

// Prevent SQL injections
$user 		= $mysqli->real_escape_string($user);
$password 	= $mysqli->real_escape_string($password);


// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//

$query = "";

require_once(TP_SQLPATH . "SUserLogin.php");

$res = $pc->Query($query);


// -------------------------------------------------------------------------------------------
//
// Use the results of the query to populate a session that shows we are logged in
//
session_start(); // Must call it since we destroyed it above.
session_regenerate_id(); // To avoid problems 

$row = $res->fetch_object();

// Must be one row in the resultset
if($res->num_rows === 1) {
	$_SESSION['idUser'] 			= $row->idUser;
	$_SESSION['accountUser'] 		= $row->accountUser;		
	$_SESSION['groupMemberUser'] 	= $row->GroupMember_idGroup;		
} else {
	$_SESSION['errorMessage']	= "Inloggningen misslyckades";
	$_POST['redirect'] 			= 'login';
}

$res->close();


// -------------------------------------------------------------------------------------------
//
// Close the connection to the database
//

$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Redirect to another page
// Support $redirect to be local uri within site or external site (starting with http://)
//
require_once(TP_SOURCEPATH . 'CHTMLPage.php');

$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'home';

CHTMLPage::redirectTo($redirect);
exit;


?>