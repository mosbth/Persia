<?php
// ===========================================================================================
//
// File: P403.php
//
// Description: Generate a 403 header and print message, could also logg into database.
//
// Author: Mikael Roos, mos@bth.se
//


// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
$pc = CPageController::GetInstance();
$pc->LoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// User controller, get info about the current user
//
//$uc 		= CUserController::GetInstance();
//$userId	= $uc->GetAccountId();


// -------------------------------------------------------------------------------------------
//
// Interception Filter, controlling access, authorithy and other checks.
//
$intFilter = CInterceptionFilter::GetInstance();
$intFilter->FrontControllerIsVisitedOrDie();


// -------------------------------------------------------------------------------------------
//
// Page specific code
//
global $gPage;

$message = $pc->GetAndClearSessionMessage($gPage);
$message = (empty($message)) ? '' : CHTMLHelpers::GetHTMLUserFeedbackNegative($message);

$htmlMain = <<<EOD
<h1>{$pc->lang['403_HEADER']}</h1>
<p>{$pc->lang['403_DESCRIPTION']}</p>
<p>{$message}</p>
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

header("HTTP/1.0 403 Forbidden");
$page->printPage($pc->lang['403_TITLE'], "", $htmlMain, "");
exit;

?>