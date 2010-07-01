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
// File: PAccountForgotPassword2.php
//
// Description: Aid for those who forgets their password. Step 2.
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
$if->CustomFilterIsSetOrDie('resetPassword');


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
// Always check whats coming in...
//
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
<div class='section'>
	<h1>{$pc->lang['FORGOT_PWD_HEADER']}</h1>
	{$messages['mailSuccess']}
	<p>{$pc->lang['FORGOT_PWD_DESCRIPTION']}</p>
</div> <!-- section -->

<div class='section'>
	<form action='{$action}' method='post'>
		<input type='hidden' name='redirect' 			value='{$redirect}'>
		<input type='hidden' name='redirect-fail' value='{$redirectFail}'>
		
		<fieldset class='standard type-1'>
	 		<!--<legend></legend>-->
		 	<div class='form-wrapper'>

				<label for="key2">{$pc->lang['FORGOT_PWD_KEY_LABEL']}</label>
				<input class='key2' type='text' name='key2' value='{$key2}' autofocus>
				
				<label for="captcha">{$pc->lang['FORGOT_PWD_MAGIC']}</label>
				<div class='captcha'>{$captchaHtml}</div>
				
				<div class='buttonbar'>
					<button type='submit' name='submit' value='verify-key'>{$pc->lang['VERIFY_KEY']}</button>
				</div> <!-- buttonbar -->

				<div class='form-status'>{$messages['forgotPwdFailed']}</div> 
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
CHTMLPage::GetInstance()->printPage($pc->lang['FORGOT_PWD_TITLE'], $htmlLeft, $htmlMain, $htmlRight);
exit;


?>