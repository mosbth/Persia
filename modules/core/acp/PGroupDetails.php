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
// File: PGroupDetails.php
//
// Description: Show and edit details of a group.
//
// Author: Mikael Roos, mos@bth.se
//
// Known issues:
// -
//
// History: 
// 2010-06-24: Created.
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
$if->UserIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
// Always check whats coming in...
//
$id	= $pc->IsNumericOrDie(strip_tags($pc->GETisSetOrSetDefault('id')), 1);


// -------------------------------------------------------------------------------------------
//
// Get details from database
//
$mysqli = $db->Connect();
$query 	= <<< EOD
CALL {$db->_['PGroupDetails']}('{$id}');
EOD;
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

// Fetch and use the results
$row = $results[0]->fetch_object();
$id 					= $row->id;
$name 				= $row->name;
$description 	= $row->description;
$members 			= $row->members;

$results[0]->close();
$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// ACP. Include the menu-bar for the User Control Panel.
//
$htmlCp = "";
require(dirname(__FILE__) . '/IAdminControlPanel.php');


// -------------------------------------------------------------------------------------------
//
// Create the HTML
//
global $gModule;

$action 	= "?m={$gModule}&amp;p=acp-groupdetailsp";
$redirect = "?m={$gModule}&amp;p=acp-groupdetails&amp;id={$id}";

// Get and format messages from session if they are set
$helpers = new CHTMLHelpers();
$messages = $helpers->GetHTMLForSessionMessages(
	Array('success'), 
	Array('failed'));

$htmlMain = <<<EOD
{$htmlCp}

<div class='section'>
	<form action='{$action}' method='post'>
		<input type='hidden' name='redirect' 				value='{$redirect}'>
		<input type='hidden' name='redirect-fail' 	value='{$redirect}'>
		<input type='hidden' name='id' 							value='{$id}'>
		
		<fieldset class='standard type-2'>
	 		<legend>{$pc->lang['GROUP_DETAILS_CAPTION']}</legend>
		 	<div class='form-wrapper'>

				<label for="name">{$pc->lang['GROUP_DETAILS_NAME']}</label>
				<input name='name' type='text' value='{$name}' maxlength='{$db->_['CSizeGroupName']}' autofocus>

				<label for='description'>{$pc->lang['GROUP_DETAILS_DESCRIPTION']}</label>
				<textarea name='description' rows='2' maxlength='{$db->_['CSizeGroupDescription']}'>{$description}</textarea>

				<div class='buttonbar'>
					<button type='submit' class='delete' name='do-submit' value='delete-group'>{$pc->lang['GROUP_DELETE']}</button>
					<button type='submit' class='save' name='do-submit' value='save-group'>{$pc->lang['GROUP_SAVE']}</button>
				</div> <!-- buttonbar -->

				<div class='form-status'>{$messages['success']}{$messages['failed']}</div> 
		 </div> <!-- wrapper -->
		</fieldset>
	</form>
</div> <!-- section -->

EOD;

$htmlLeft 	= "";
$htmlRight	= <<<EOD
<!--
<h3 class='columnMenu'></h3>
<p>
Later...
</p>
-->

EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
CHTMLPage::GetInstance()->PrintPage(sprintf($pc->lang['GROUP_DETAILS_TITLE'], $name), $htmlLeft, $htmlMain, $htmlRight);
exit;


?>