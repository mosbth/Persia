<?php
// ===========================================================================================
//
// File: PLogin.php
//
// Description: Show a login-form, ask for user name and password.
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


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$account	= strip_tags($pc->GetAndClearSessionMessage('loginAccount'));


// -------------------------------------------------------------------------------------------
//
// Always redirect to latest visited page on success.
//
$redirectTo = $pc->SESSIONisSetOrSetDefault('history2');


// -------------------------------------------------------------------------------------------
//
// Show the login-form
//
global $gModule;

$action 			= "?m={$gModule}&amp;p=loginp";
$redirect 		= $redirectTo;
$redirectFail = "?m={$gModule}&amp;p=login";

// Get and format messages from session if they are set
$helpers = new CHTMLHelpers();
$messages = $helpers->GetHTMLForSessionMessages(
	Array(), 
	Array('loginFailed'));

// Only display LDAP login button if LDAP is enabled.
$ldapButton = <<<EOD
<tr>
<td colspan='2' style='text-align: right;'>
<button type='submit' name='submit' value='login-ldap'>Login (LDAP)</button>
</td>
</tr>
EOD;
$ldapButton = defined('LDAP_AUTH_SERVER') ? $ldapButton : '';

// Only display if enabled
$createNewUser = "[<a href='?m={$gModule}&amp;p=account-create'>{$pc->lang['CREATE_NEW_ACCOUNT']}</a>] ";
$createNewUser = CREATE_NEW_ACCOUNT ? $createNewUser : '';

// Only display if enabled
$forgotPassword = "[<a href='?m={$gModule}&amp;p=account-forgot-pwd'>{$pc->lang['FORGOT_PASSWORD']}</a>] ";
$forgotPassword = FORGOT_PASSWORD ? $forgotPassword : '';


// Create main HTML
$htmlMain = <<<EOD
<h1>{$pc->lang['LOGIN']}</h1>

<p>{$pc->lang['LOGIN_INTRO_TEXT']}</p> <!-- {$pc->lang['LOGIN_USING_ACCOUNT_OR_EMAIL']} -->

<form action='{$action}' method='POST'>
<input type='hidden' name='redirect' 			value='{$redirect}'>
<input type='hidden' name='redirect-fail' value='{$redirectFail}'>

<fieldset class='loginform'>
<table width='99%'>
<tr>
<td><label for="account">{$pc->lang['USER']}</label></td>
<td style='text-align: right;'><input class='account' type='text' name='account' value='{$account}'></td>
</tr>
<tr>
<td><label for="account">{$pc->lang['PASSWORD']}</label></td>
<td style='text-align: right;'><input class='password' type='password' name='password'></td>
</tr>

<tr>
<td colspan='2' style='text-align: right;'>
<button type='submit' name='submit' value='login-local'>{$pc->lang['LOGIN']}</button>
</td>
</tr>

{$ldapButton}

<tr><td colspan='2'>{$messages['loginFailed']}</td></tr>

</table>
</fieldset>
</form>

<p>{$createNewUser}{$forgotPassword}</p>

EOD;

$htmlLeft 	= "";
$htmlRight	= <<<EOD
<section>
<h3 class='columnMenu'>Various ways to sign in</h3>
<p>
Now supporting both local database and LDAP.
</p>
</section>

EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->printPage($pc->lang['LOGIN'], $htmlLeft, $htmlMain, $htmlRight);
exit;

?>