<?php
// ===========================================================================================
//
// File: PAccountCreate.php
//
// Description: Form to create a new account.
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
$intFilter->UserIsSignedInOrRecirectToSignIn();
$intFilter->UserIsCurrentUserOrMemberOfGroupAdminOr403($userId);


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
// Always check whats coming in...
//
$account	= strip_tags($pc->POSTorSESSIONisSetOrSetDefaultClearSESSION('account', ''));


// -------------------------------------------------------------------------------------------
//
// Always redirect to latest visited page on success.
//
$redirectTo = $pc->SESSIONisSetOrSetDefault('history2');


// -------------------------------------------------------------------------------------------
//
// Prepare the CAPTCHA
//
$captcha = new CCaptcha();
$captchaStyle = strip_tags($pc->GETIsSetOrSetDefault('captcha-style', 'custom'));
$captchaHtml = $captcha->GetHTMLToDisplay($captchaStyle);


// -------------------------------------------------------------------------------------------
//
// Show the login-form
//
global $gModule;

$action 			= "?m={$gModule}&amp;p=account-createp";
$redirect 		= "?m={$gModule}&amp;p=account-settings";
$redirectFail = "?m={$gModule}&amp;p=account-create";
$silentLogin 	= "?m={$gModule}&amp;p=loginp";

// Get and format messages from session if they are set
$helpers = new CHTMLHelpers();
$messages = $helpers->GetHTMLForSessionMessages(
	Array(), 
	Array('createAccountFailed'));

$htmlMain = <<<EOD
<h1>{$pc->lang['CREATE_NEW_ACCOUNT_TITLE']}</h1>

<p>{$pc->lang['CHOOSE_NAME_AND_PASSWORD']}</p>

<form action='{$action}' method='POST'>
<input type='hidden' name='redirect' 			value='{$redirect}'>
<input type='hidden' name='redirect-fail' value='{$redirectFail}'>
<input type='hidden' name='silent-login' 	value='{$silentLogin}'>

<fieldset class='accountsettings'>
<table width='99%'>

<tr>
<td><label for="account">{$pc->lang['ACCOUNT_NAME_LABEL']}</label></td>
<td style='text-align: right;'><input class='account' type='text' name='account' value='{$account}'></td>
</tr>

<tr>
<td><label for="account">{$pc->lang['ACCOUNT_PASSWORD_LABEL']}</label></td>
<td style='text-align: right;'><input class='password' type='password' name='password1'></td>
</tr>

<tr>
<td><label for="account">{$pc->lang['ACCOUNT_PASSWORD_AGAIN_LABEL']}</label></td>
<td style='text-align: right;'><input class='password' type='password' name='password2'></td>
</tr>

<tr>
<td><label for="captcha">{$pc->lang['ACCOUNT_NAME_MAGIC']}</label></td>
<td><div style='float: right'>{$captchaHtml}</div></td>
</tr>

<tr>
<td colspan='2' style='text-align: right;'>
<button type='submit' name='submit' value='account-create'>{$pc->lang['CREATE_ACCOUNT']}</button>
</td>
</tr>

<tr><td colspan='2'>
{$messages['createAccountFailed']}
</td></tr>

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

$page->printPage($pc->lang['CREATE_NEW_ACCOUNT_TITLE'], $htmlLeft, $htmlMain, $htmlRight);
exit;

?>