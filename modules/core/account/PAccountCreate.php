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
$pc = new CPageController();
$pc->LoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// Interception Filter, controlling access, authorithy and other checks.
//
$intFilter = new CInterceptionFilter();

$intFilter->FrontControllerIsVisitedOrDie();
//$intFilter->UserIsSignedInOrRecirectToSignIn();
//$intFilter->UserIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$account	= $pc->GETisSetOrSetDefault('account', "");

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
$captchaHtml = $captcha->GetHTMLToDisplay();


// -------------------------------------------------------------------------------------------
//
// Show the login-form
//
global $gModule;

$action 			= "?m={$gModule}&amp;p=account-createp";
$redirect 		= "?m={$gModule}&amp;p=account-settings";
$redirectFail = "?m={$gModule}&amp;p=account-create";
$silentLogin 	= "?m={$gModule}&amp;p=loginp";

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
<td colspan='2''>
<div style='float: right'>
{$captchaHtml}
</div>
</td>
</tr>

<tr>
<td colspan='2' style='text-align: right;'>
<button type='submit' name='submit' value='account-create'>{$pc->lang['CREATE_ACCOUNT']}</button>
</td>
</tr>



</table>
</fieldset>

</form>

EOD;

$htmlLeft 	= "";
$htmlRight	= <<<EOD
<h3 class='columnMenu'>Various ways to sign in</h3>
<p>
Later...
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