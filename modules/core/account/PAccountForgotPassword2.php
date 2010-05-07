<?php
// ===========================================================================================
//
// File: PAccountForgotPassword2.php
//
// Description: Aid for those who forgets their password. Step 2.
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
$intFilter->CustomFilterIsSetOrDie('resetPassword');


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
//$account	= strip_tags($pc->POSTorSESSIONisSetOrSetDefaultClearSESSION('account', ''));
$key2	= $pc->SESSIONisSetOrSetDefault('key2', '');


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

$action 			= "?m={$gModule}&amp;p=account-forgot-pwd2p";
$redirect 		= "?m={$gModule}&amp;p=account-forgot-pwd3";
$redirectFail = "?m={$gModule}&amp;p=account-forgot-pwd2";
$silentLogin 	= "?m={$gModule}&amp;p=loginp";

// Get and format messages from session if they are set
$helpers = new CHTMLHelpers();
$messages = $helpers->GetHTMLForSessionMessages(
	Array('mailSuccess'), 
	Array('forgotPwdFailed'));

$htmlMain = <<<EOD
<h1>{$pc->lang['FORGOT_PWD_HEADER']}</h1>

{$messages['mailSuccess']}
<p>{$pc->lang['FORGOT_PWD_DESCRIPTION']}</p>

<form action='{$action}' method='POST'>
<input type='hidden' name='redirect' 			value='{$redirect}'>
<input type='hidden' name='redirect-fail' value='{$redirectFail}'>

<fieldset class='accountsettings'>
<table width='99%'>

<tr>
<td><label for="key2">{$pc->lang['FORGOT_PWD_KEY_LABEL']}</label></td>
<td style='text-align: right;'><input class='account' type='text' name='key2' value='{$key2}' autofocus></td>
</tr>

<tr>
<td><label for="captcha">{$pc->lang['FORGOT_PWD_MAGIC']}</label></td>
<td><div style='float: right'>{$captchaHtml}</div></td>
</tr>

<tr>
<td colspan='2' style='text-align: right;'>
<button type='submit' name='submit' value='verify-key'>{$pc->lang['VERIFY_KEY']}</button>
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