<?php
// ===========================================================================================
//
// File: PAccountForgotPassword1.php
//
// Description: Aid for those who forgets their password. Step 1.
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


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
// Always check whats coming in...
//
$account	= strip_tags($pc->POSTorSESSIONisSetOrSetDefaultClearSESSION('account', ''));


// -------------------------------------------------------------------------------------------
//
// Prepare the CAPTCHA
//
$captcha = new CCaptcha();
$captchaStyle = strip_tags($pc->GETIsSetOrSetDefault('captcha-style', 'custom'));
$captchaHtml = $captcha->GetHTMLToDisplay($captchaStyle);


// -------------------------------------------------------------------------------------------
//
// Show the form
//
global $gModule;

$action 			= "?m={$gModule}&amp;p=account-forgot-pwdp";
$redirect			= "?m={$gModule}&amp;p=account-forgot-pwd2";
$redirectFail = "?m={$gModule}&amp;p=account-forgot-pwd";

// Get and format messages from session if they are set
$helpers = new CHTMLHelpers();
$messages = $helpers->GetHTMLForSessionMessages(
	Array(), 
	Array('forgotPwdFailed'));

$htmlMain = <<<EOD
<h1>{$pc->lang['FORGOT_PWD_HEADER']}</h1>

<p>{$pc->lang['FORGOT_PWD_DESCRIPTION']}</p>

<form action='{$action}' method='POST'>
<input type='hidden' name='redirect' 			value='{$redirect}'>
<input type='hidden' name='redirect-fail' value='{$redirectFail}'>

<fieldset class='accountsettings'>
<table width='99%'>

<tr>
<td><label for="account">{$pc->lang['FORGOT_PWD_ACCOUNT_NAME_LABEL']}</label></td>
<td style='text-align: right;'><input class='account' type='text' name='account' value='{$account}' autofocus></td>
</tr>

<tr>
<td><label for="captcha">{$pc->lang['FORGOT_PWD_MAGIC']}</label></td>
<td><div style='float: right'>{$captchaHtml}</div></td>
</tr>

<tr>
<td colspan='2' style='text-align: right;'>
<button type='submit' name='submit' value='send-rescue-mail'>{$pc->lang['SEND_RESCUE_MAIL']}</button>
</td>
</tr>

<tr><td colspan='2'>{$messages['forgotPwdFailed']}</td></tr>

</table>
</fieldset>

</form>

EOD;

//
// Enable changing and referencing parts of the current url
//
$links  = "<a href='" . $pc->ModifyCurrentURL('captcha-style=red') . 				"'>red</a> ";
$links .= "<a href='" . $pc->ModifyCurrentURL('captcha-style=white') . 			"'>white</a> ";
$links .= "<a href='" . $pc->ModifyCurrentURL('captcha-style=blackglass') . "'>blackglass</a> ";
$links .= "<a href='" . $pc->ModifyCurrentURL('captcha-style=clean') . 			"'>clean</a> ";
$links .= "<a href='" . $pc->ModifyCurrentURL('captcha-style=custom') . 		"'>custom</a> ";

$htmlLeft 	= "";
$htmlRight	= <<<EOD
<h3 class='columnMenu'>Style the reCAPTCHA widget</h3>
<p>
{$links}
</p>

EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->printPage($pc->lang['FORGOT_PWD_TITLE'], $htmlLeft, $htmlMain, $htmlRight);
exit;

?>