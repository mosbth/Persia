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
// File: PAccountSettings.php
//
// Description: Show the users profile information in a form and make it possible to edit 
// the information.
//
// Author: Mikael Roos, mos@bth.se
//
// Known issues:
// Update to include and show off "all" features.
//
// History: 
// 2010-06-16: New structure for instantiating controllers. Included license message.
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
$if->UserIsSignedInOrRedirectToSignIn();
$if->UserIsCurrentUserOrMemberOfGroupAdminOr403($uc->GetAccountId());


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
// Always check whats coming in...
// 
$userId = $uc->GetAccountId();


// -------------------------------------------------------------------------------------------
//
// Connect to the database, create the query and execute it, take care of the results.
//
$mysqli = $db->Connect();

$query = <<< EOD
CALL {$db->_['PGetAccountDetails']}({$userId});
EOD;

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);
	
// Get account details 	
$row = $results[0]->fetch_object();
$account 				= $row->account;
$name						= $row->name;
$mail						= $row->email;
$avatar 				= $row->avatar;
$gravatar 			= $row->gravatar;
$gravatarsmall	= $row->gravatarsmall;
$results[0]->close(); 

// Get group memberships details 	
$htmlGroups = <<<EOD
<table class='standard'>
<caption></caption>
<thead>
<tr>
<th>{$pc->lang['GROUP_TH_NAME']}</th>
<th>{$pc->lang['GROUP_TH_DESCRIPTION']}</th>
</tr>
</thead>
<tbody>
EOD;
$i=0;
while($row = $results[1]->fetch_object()) {    
	$htmlGroups .= "<tr class='r".($i++%2+1)."'><td>{$row->groupname}</td><td>{$row->groupdescription}</td></tr>";
}
$htmlGroups .= <<<EOD
</tbody>
<tfoot></tfoot>
</table>
EOD;
$results[1]->close(); 

$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// UCP. Include the menu-bar for the User Control Panel.
//
$htmlCp = "";
require(dirname(__FILE__) . '/IUserControlPanel.php');


// -------------------------------------------------------------------------------------------
//
// Page specific code
//
global $gModule;

$action 	= "?m={$gModule}&amp;p=ucp-account-update";
$redirect = "?m={$gModule}&amp;p=ucp-account-settings";
$imageLink = WS_IMAGES;

// Get and format messages from session if they are set
$helpers = new CHTMLHelpers();
$messages = $helpers->GetHTMLForSessionMessages(
	Array('mailSuccess', 'changePwdSuccess'),
	Array('mailFailed', 'changePwdFailed'));

$htmlMain = <<< EOD
{$htmlCp}
<div class='section'>
	<p>{$pc->lang['SETTINGS_DESCRIPTION']}</p>
</div> <!-- section -->

<div id='s-pwd' class='section'>
	<form action='{$action}' method='post'>
		<input type='hidden' name='redirect' 					value='{$redirect}'>
		<input type='hidden' name='redirect-failure' 	value='{$redirect}'>
		<input type='hidden' name='accountid' 				value='{$userId}'>
		<fieldset class='standard type-1 account-settings'>
	 		<legend>{$pc->lang['BASIC_ACCOUNT_INFO']}</legend>
		 	<div class='form-wrapper'>
				<p>{$pc->lang['DESCRIPTION_ACCOUNT']}</p>
				<label for="account">{$pc->lang['ACCOUNT_NAME_LABEL']}</label>
				<input class='account' type='text' name='account' readonly='readonly' value='{$account}'>
				<label for="password1">{$pc->lang['ACCOUNT_PASSWORD_LABEL']}</label>
				<input class='password' type='password' name='password1'>
				<label for="password2">{$pc->lang['ACCOUNT_PASSWORD_AGAIN_LABEL']}</label>
				<input class='password' type='password' name='password2'>
				<button type='submit' name='submit' value='change-password'>{$pc->lang['CHANGE_PASSWORD']}</button>
				<div class='form-status'>{$messages['changePwdSuccess']}{$messages['changePwdFailed']}</div> 
		 </div> <!-- wrapper -->
		</fieldset>
	</form>
</div> <!-- section -->


<div id='s-mail' class='section'>
	<form action='{$action}' method='post'>
		<input type='hidden' name='redirect' 					value='{$redirect}#s-mail'>
		<input type='hidden' name='redirect-failure' 	value='{$redirect}#s-mail'>
		<input type='hidden' name='accountid' 				value='{$userId}'>
		<fieldset class='standard type-1 account-settings'>
	 		<legend>{$pc->lang['MAIL_SETTINGS']}</legend>
		 	<div class='form-wrapper'>
				<p>{$pc->lang['DESCRIPTION_MAIL']}</p>
				<label for='mail'>{$pc->lang['MAIL_LABEL']}</label>
				<input id='mail' class='mail' type='email' name='mail' value='{$mail}' placeholder="{$pc->lang['INSERT_MAIL_HERE']}" autocomplete
					required pattern="^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*\.(\w{2}|(com|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum))$" title="{$pc->lang['MAIL_FORMAT_REQUIRED']}">
				<button type='submit' name='submit' value='change-mail'>{$pc->lang['UPDATE_MAIL']}</button>
				<div class='form-status'>{$messages['mailSuccess']}{$messages['mailFailed']}</div> 
		 </div> <!-- wrapper -->
		</fieldset>
	</form>
</div> <!-- section -->


<div id='s-avatar' class='section'>
	<form action='{$action}' method='post'>
		<input type='hidden' name='redirect' 					value='{$redirect}#s-avatar'>
		<input type='hidden' name='redirect-failure' 	value='{$redirect}#s-avatar'>
		<input type='hidden' name='accountid' 				value='{$userId}'>
		<fieldset class='standard type-1 account-settings'>
	 		<legend>{$pc->lang['AVATAR_SETTINGS']}</legend>
		 	<div class='form-wrapper'>
				<p>{$pc->lang['AVATAR_INFO']}</p>
				<label for='avatar'>{$pc->lang['AVATAR_LABEL']}</label>
				<input id ='avatar' class='avatar' type='url' list='avatars' name='avatar' value='{$avatar}' placeholder="{$pc->lang['INSERT_LINK_TO_AVATAR_HERE']}" autocomplete>
					<!--
					<datalist id='avatars'>
					<option>{$imageLink}man_60x60.png</option>
					<option>{$imageLink}woman_60x60.png</option>
					<option>{$imageLink}egg_60x60.png</option>
					</datalist>
					-->
				<button type='submit' name='submit' value='change-avatar'>{$pc->lang['UPDATE_AVATAR']}</button>
				<div class='form-status'><img src='{$avatar}' alt=''></div> 
		 </div> <!-- wrapper -->
		</fieldset>
	</form>
</div> <!-- section -->


<div id='s-gravatar' class='section'>
	<form action='{$action}' method='post'>
		<input type='hidden' name='redirect' 					value='{$redirect}#s-gravatar'>
		<input type='hidden' name='redirect-failure' 	value='{$redirect}#s-gravatar'>
		<input type='hidden' name='accountid' 				value='{$userId}'>
		<fieldset class='standard type-1 account-settings'>
	 		<legend>{$pc->lang['GRAVATAR_SETTINGS']}</legend>
		 	<div class='form-wrapper'>
				<p>{$pc->lang['GRAVATAR_INFO']}</p>
				<label for='gravatar'>{$pc->lang['GRAVATAR_LABEL']}</label>
				<input id='gravatar' class='gravatar' name='gravatar' type='email' value='{$gravatar}' placeholder="{$pc->lang['INSERT_EMAIL_FOR_GRAVATAR_HERE']}" autocomplete>
				<button type='submit' name='submit' value='change-gravatar'>{$pc->lang['UPDATE_GRAVATAR']}</button>
				<div class='form-status'><img src='{$gravatarsmall}' alt=''></div> 
		 </div> <!-- wrapper -->
		</fieldset>
	</form>
</div> <!-- section -->


<div id='a-groups' class='section'>
	<form>
		<fieldset class='standard type-1 account-settings'>
			<legend>{$pc->lang['GROUP_SETTINGS']}</legend>
			<div class='form-wrapper'>
				<p>{$pc->lang['GROUPMEMBER_OF_LABEL']}</p>
				{$htmlGroups}
			</div> <!-- wrapper -->
		</fieldset>
	</form>
</div> <!-- section -->

EOD;


$htmlLeft 	= "";
$htmlRight	= <<<EOD
<!--
<h3 class='columnMenu'>About Privacy</h3>
<p>
Later...
</p>
-->

EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
CHTMLPage::GetInstance()->PrintPage(sprintf($pc->lang['SETTINGS_FOR'], $uc->GetAccountName()), $htmlLeft, $htmlMain, $htmlRight);
exit;


?>