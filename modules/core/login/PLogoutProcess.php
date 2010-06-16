<?php
// ===========================================================================================
//
// File: PLogoutProcess.php
//
// Description: Logout by destroying the session.
//
// Author: Mikael Roos, mos@bth.se


// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
$pc = CPageController::GetInstance();
//$pc->LoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// User controller, get info about the current user
//
$uc 		= CUserController::GetInstance();
$userId	= $uc->GetAccountId();


// -------------------------------------------------------------------------------------------
//
// Interception Filter, controlling access, authorithy and other checks.
//
$intFilter = CInterceptionFilter::GetInstance();
$intFilter->FrontControllerIsVisitedOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
// Always check whats coming in...
// 
$redirectTo = $pc->SESSIONisSetOrSetDefault('history2');


// -------------------------------------------------------------------------------------------
//
// Destroy the current session (logout user), if it exists. 
//
require_once(TP_SOURCEPATH . 'FDestroySession.php');


// -------------------------------------------------------------------------------------------
//
// Redirect to the latest page visited before logout.
//
$pc->RedirectTo($redirectTo);
exit;


?>