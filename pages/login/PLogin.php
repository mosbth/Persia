<?php
// ===========================================================================================
//
// PLogin.php
//
// Show a login-form, ask for user name and password.
//


// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
require_once(TP_SOURCEPATH . 'CPagecontroller.php');

$pc = new CPagecontroller();
$pc->LoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// Interception Filter, access, authorithy and other checks.
//
require_once(TP_SOURCEPATH . 'CInterceptionFilter.php');

$intFilter = new CInterceptionFilter();

$intFilter->frontcontrollerIsVisitedOrDie();
//$intFilter->userIsSignedInOrRecirectToSign_in();
//$intFilter->userIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of global pageController settings, can exist for several pagecontrollers.
// Decide how page is displayed, review CHTMLPage for supported types.
//
$displayAs = $pc->GETisSetOrSetDefault('pc_display', '');


// -------------------------------------------------------------------------------------------
//
// Enable access to protected pages through direct-links.
// Try access a protected file, redirect to login, redirect back to protected page.
//
$redirectTo = 'home';
if($gPage != 'login') {
	$refToThisPage	= "http://" . $_SERVER['HTTP_HOST'] . ":" . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
	$redirectTo 	= $refToThisPage;
}


// -------------------------------------------------------------------------------------------
//
// Page specific code
//


// -------------------------------------------------------------------------------------------
//
// Show the login-form
//
$htmlMain = <<<EOD
<h1>{$pc->lang['LOGIN']}</h1>
<p>
{$pc->lang['LOGIN_INTRO_TEXT']}
</p>
EOD;

$htmlLeft = "";

$htmlRight = <<<EOD
<div class='sidebox'>
<div id='login'>
<fieldset>
<p>
{$pc->lang['LOGIN_USING_ACCOUNT_OR_EMAIL']}
</p>
<form action="?p=loginp" method="post">
<input type='hidden' name='redirect' value='{$redirectTo}'>
<table>
<tr>
<td style="text-align: right">
<label for="nameUser">{$pc->lang['USER']}</label>
</td>
<td>
<input class="login" type="text" name="nameUser">
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
</div> <!-- #login -->
</div> <!-- .sidebox -->

EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
require_once(TP_SOURCEPATH . 'CHTMLPage.php');

$page = new CHTMLPage(WS_STYLESHEET);

$page->printPage($htmlLeft, $htmlMain, $htmlRight, $pc->lang['LOGIN'], $displayAs);
exit;

?>