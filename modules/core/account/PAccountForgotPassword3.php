<?php
// ===========================================================================================
//
// File: PAccountForgotPassword3.php
//
// Description: Aid for those who forgets their password. Step 3.
//
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
// File: PAccountForgotPassword3.php
//
// Description: Aid for those who forgets their password. Step 3.
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
$account	= $pc->SESSIONisSetOrSetDefault('accountName', '');


// -------------------------------------------------------------------------------------------
//
// Show the form
//
global $gModule;

$action 			= "?m={$gModule}&amp;p=account-forgot-pwd3p";
$redirect 		= "?m={$gModule}&amp;p=account-forgot-pwd4";
$redirectFail = "?m={$gModule}&amp;p=account-forgot-pwd3";
$silentLogin 	= "?m={$gModule}&amp;p=loginp";

// Get and format messages from session if they are set
$helpers = new CHTMLHelpers();
$messages = $helpers->GetHTMLForSessionMessages(
	Array('keySuccess'), 
	Array('changePwdFailed'));

$htmlMain = <<<EOD
<div class='section'>
	<h1>{$pc->lang['FORGOT_PWD_HEADER']}</h1>
	{$messages['keySuccess']}
	<p>{$pc->lang['FORGOT_PWD_DESCRIPTION']}</p>
</div> <!-- section -->

<div class='section'>
	<form action='{$action}' method='post'>
		<input type='hidden' name='redirect' 			value='{$redirect}'>
		<input type='hidden' name='redirect-fail'	value='{$redirectFail}'>
		<input type='hidden' name='silent-login' 	value='{$silentLogin}'>
		
		<fieldset class='standard type-1'>
	 		<!--<legend></legend>-->
		 	<div class='form-wrapper'>
				
				<label for="account">{$pc->lang['ACCOUNT_NAME_LABEL']}</label>
				<input class='account' type='text' name='account' readonly='readonly' value='{$account}'>
				
				<label for="password1">{$pc->lang['ACCOUNT_PASSWORD_LABEL']}</label>
				<input class='password' type='password' name='password1' autofocus>
				
				<label for="password2">{$pc->lang['ACCOUNT_PASSWORD_AGAIN_LABEL']}</label>
				<input class='password' type='password' name='password2'>
				
				<div class='buttonbar'>
					<button type='submit' name='submit' value='change-password'>{$pc->lang['CHANGE_PASSWORD']}</button>
				</div> <!-- buttonbar -->

				<div class='form-status'>{$messages['changePwdFailed']}</div> 
		 </div> <!-- wrapper -->
		</fieldset>
	</form>
</div> <!-- section -->


EOD;

//
// 
//
$htmlLeft 	= "";
$htmlRight	= <<<EOD
<!--
<h3 class='columnMenu'></h3>
<p></p>
-->

EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
CHTMLPage::GetInstance()->printPage($pc->lang['FORGOT_PWD_TITLE'], $htmlLeft, $htmlMain, $htmlRight);
exit;


?>