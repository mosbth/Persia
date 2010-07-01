<?php
// ===========================================================================================
//
//		Persia (http://phpersia.org), software to build webbapplications.
//    Copyright (C) 2010  Mikael Roos (mos@bth.se)
//
//    This file is part of Persia.
//
//    Persia is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    Persia is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with Persia. If not, see <http://www.gnu.org/licenses/>.
//
// File: PAccountCreate.php
//
// Description: Form to create a new account.
//
// Author: Mikael Roos, mos@bth.se
//
// Known issues:
// -
//
// History: 
// 2010-07-01: New structure for instantiating controllers. Included license message.
//


// -------------------------------------------------------------------------------------------
//
// Get common controllers, uncomment if not used in current pagecontroller.
//
// $pc, Page Controller helpers. Useful methods to use in most pagecontrollers
// $uc, User Controller. Keeps information/permission on user currently signed in.
// $if, Interception Filter. Useful to check constraints before entering a pagecontroller.
// $db, Database Controller. Manages all database access.
//
$pc = CPageController::GetInstanceAndLoadLanguage(__FILE__);
$uc = CUserController::GetInstance();
$if = CInterceptionFilter::GetInstance();
$db = CDatabaseController::GetInstance();


// -------------------------------------------------------------------------------------------
//
// Perform checks before continuing, what's to be fullfilled to enter this controller?
//
$if->FrontControllerIsVisitedOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
// Always check whats coming in...
//
$account = trim(strip_tags($pc->POSTorSESSIONisSetOrSetDefaultClearSESSION('account', '')));


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
// Create HTML for the page.
//
global $gModule;

$action 			= "?m={$gModule}&amp;p=account-createp";
$redirect 		= "?m={$gModule}&amp;p=ucp-account-settings";
$redirectFail = "?m={$gModule}&amp;p=account-create";
$silentLogin 	= "?m={$gModule}&amp;p=loginp";

// Get and format messages from session if they are set
$helpers = new CHTMLHelpers();
$messages = $helpers->GetHTMLForSessionMessages(
	Array(), 
	Array('createAccountFailed'));

$htmlMain = <<<EOD
<div class='section'>
	<h1>{$pc->lang['CREATE_NEW_ACCOUNT_TITLE']}</h1>
	<p>{$pc->lang['CHOOSE_NAME_AND_PASSWORD']}</p>
</div> <!-- section -->

<div class='section'>
	<form action='{$action}' method='post'>
		<input type='hidden' name='redirect' 			value='{$redirect}'>
		<input type='hidden' name='redirect-fail' value='{$redirectFail}'>
		<input type='hidden' name='silent-login' 	value='{$silentLogin}'>
		
		<fieldset class='standard type-1'>
	 		<!--<legend></legend>-->
		 	<div class='form-wrapper'>

				<p></p>
				
				<label for="account">{$pc->lang['ACCOUNT_NAME_LABEL']}</label>
				<input class='account' type='text' name='account' value='{$account}'>
				
				<label for="account">{$pc->lang['ACCOUNT_PASSWORD_LABEL']}</label>
				<input class='password' type='password' name='password1'>
				
				<label for="account">{$pc->lang['ACCOUNT_PASSWORD_AGAIN_LABEL']}</label>
				<input class='password' type='password' name='password2'>
				
				<label for="captcha">{$pc->lang['ACCOUNT_NAME_MAGIC']}</label>
				<div class='captcha'>{$captchaHtml}</div>
				
				<div class='buttonbar'>
					<button type='submit' name='submit' value='account-create'>{$pc->lang['CREATE_ACCOUNT']}</button>
				</div> <!-- buttonbar -->

				<div class='form-status'>{$messages['createAccountFailed']}</div> 
		 </div> <!-- wrapper -->
		</fieldset>
	</form>
</div> <!-- section -->

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
CHTMLPage::GetInstance()->printPage($pc->lang['CREATE_NEW_ACCOUNT_TITLE'], $htmlLeft, $htmlMain, $htmlRight);
exit;

?>