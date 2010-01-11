<?php
// ===========================================================================================
//
// PLogoutProcess.php
//
// Logout by destroying the session.
//


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
// Redirect to another page
//
$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'login';
header('Location: ' . WS_SITELINK . "?p={$redirect}");
exit;


?>