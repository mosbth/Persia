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
//$intFilter->UserIsSignedInOrRecirectToSignIn();
//$intFilter->UserIsMemberOfGroupAdminOrDie();


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

/*
$htmlMain = <<<EOD
<form action="?m={$gModule}&amp;p=loginp" method="POST">
<input type='hidden' name='redirect' value='{$redirectTo}'>

<fieldset class='loginform'>
<h1>{$pc->lang['LOGIN']}</h1>
<p>{$pc->lang['LOGIN_INTRO_TEXT']}</p> 

<table>
<tr>
<td style="text-align: right">
<label for="nameUser">{$pc->lang['USER']}</label>
</td>
<td>
<input class="account" type="text" name="nameUser">
</td>
</tr>
<tr>
<td style="text-align: right">
<label for="passwordUser">{$pc->lang['PASSWORD']}</label>
</td>
<td>
<input class="password" type="password" name="passwordUser">
</td>
</tr>
<tr>
<td colspan='2' style="text-align: right">
<button type="submit" name="submit">{$pc->lang['LOGIN']}</button>
</td>
</tr>
</table>

<p>[<a href="?m={$gModule}&amp;p=account-create">{$pc->lang['CREATE_NEW_ACCOUNT']}</a>]</p>
</fieldset>

</form>

<!--
<p><a href="PGetPassword.php">Jag har glömt mitt lösenord!</a></p>
-->

EOD;
*/

$htmlMain = <<<EOD
<h1>{$pc->lang['LOGIN']}</h1>

<p>{$pc->lang['LOGIN_INTRO_TEXT']}</p> <!-- {$pc->lang['LOGIN_USING_ACCOUNT_OR_EMAIL']} -->

<form action='{$action}' method='POST'>
<input type='hidden' name='redirect' 			value='{$redirect}'>
<input type='hidden' name='redirect-fail' value='{$redirectFail}'>

<fieldset class='accountsettings'>
<table width='99%'>
<tr>
<td><label for="account">{$pc->lang['USER']}</label></td>
<td style='text-align: right;'><input class='account' type='text' name='account'></td>
</tr>
<tr>
<td><label for="account">{$pc->lang['PASSWORD']}</label></td>
<td style='text-align: right;'><input class='password' type='password' name='password'></td>
</tr>
<td colspan='2' style='text-align: right;'>
<button type='submit' name='submit' value='account-create'>{$pc->lang['LOGIN']}</button>
</td>
</tr>
</table>

</fieldset>

</form>

<p>
[<a href="?m={$gModule}&amp;p=account-create">{$pc->lang['CREATE_NEW_ACCOUNT']}</a>]
</p>

EOD;

$htmlLeft 	= "";
$htmlRight	= <<<EOD
<section>
<h3 class='columnMenu'>Various ways to sign in</h3>
<p>
Later...
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