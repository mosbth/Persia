<?php
// ===========================================================================================
//
// File: PAccountForgotPassword4.php
//
// Description: Aid for those who forgets their password. Step 4.
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
$intFilter->CustomFilterIsSetOrDie('resetPassword', 'unset');
$intFilter->UserIsSignedInOrRecirectToSignIn();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
// Always check whats coming in...
// 


// -------------------------------------------------------------------------------------------
//
// Show the form
//
global $gModule;

$linkToProfile = sprintf($pc->lang['LINK_TO_ACCOUNT_PROFILE'], "?m={$gModule}&amp;p=account-settings");

// Get and format messages from session if they are set
$helpers = new CHTMLHelpers();
$messages = $helpers->GetHTMLForSessionMessages(
	Array('changePwdSuccess'), 
	Array());

$htmlMain = <<<EOD
<h1>{$pc->lang['FORGOT_PWD_HEADER']}</h1>
{$messages['changePwdSuccess']}

<p>{$pc->lang['FORGOT_PWD_DESCRIPTION']}</p>
<p>{$linkToProfile}</p>

EOD;

//
// 
//
$htmlLeft 	= "";
$htmlRight	= <<<EOD
<h3 class='columnMenu'></h3>
<p></p>

EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->printPage($pc->lang['FORGOT_PWD_TITLE'], $htmlLeft, $htmlMain, $htmlRight);
exit;

?>