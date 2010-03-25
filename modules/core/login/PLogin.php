<?php
// ===========================================================================================
//
// PLogin.php
//
// Show a login-form, ask for user name and password.
//
// Author: Mikael Roos
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

$htmlMain = <<<EOD
<fieldset class='loginform'>
<h1>{$pc->lang['LOGIN']}</h1>
<p>
{$pc->lang['LOGIN_INTRO_TEXT']}
</p>
<p>
<!-- {$pc->lang['LOGIN_USING_ACCOUNT_OR_EMAIL']} -->
</p>
<form action="?m={$gModule}&p=loginp" method="post">
<input type='hidden' name='redirect' value='{$redirectTo}'>
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
</form>
</fieldset>
<!--
<p><a href="PGetPassword.php">Skapa en ny användare!</a></p>
<p><a href="PGetPassword.php">Jag har glömt mitt lösenord!</a></p>
-->

EOD;

$htmlLeft  = "";
$htmlRight = "";


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->printPage('Login', $htmlLeft, $htmlMain, $htmlRight);
exit;


?>