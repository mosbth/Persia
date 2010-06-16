<?php
// ===========================================================================================
//
// File: PUserControlPanel.php
//
// Description: First page for User Control Panel (ucp). Display menu.
//
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
$uc 		= CUserController::GetInstance();
$userId	= $uc->GetAccountId();


// -------------------------------------------------------------------------------------------
//
// Interception Filter, controlling access, authorithy and other checks.
//
$intFilter = CInterceptionFilter::GetInstance();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();
$intFilter->UserIsCurrentUserOrMemberOfGroupAdminOr403($userId);


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
// Always check whats coming in...
// 


// -------------------------------------------------------------------------------------------
//
// Include the menu-bar for the User Control Panel.
//
$htmlMenuBar = "";
include(dirname(__FILE__) . '/IUserControlPanel.php');


// -------------------------------------------------------------------------------------------
//
// Create HTML for page
//
$htmlMain = <<< EOD
{$htmlMenuBar}

EOD;

$htmlLeft 	= "";
$htmlRight	= <<<EOD
<!--
<h3 class='columnMenu'>Tags</h3>
<p>
Later...
</p>
-->
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->PrintPage(sprintf($pc->lang['UCP_FOR'], $uc->GetAccountName()), $htmlLeft, $htmlMain, $htmlRight);
exit;


?>