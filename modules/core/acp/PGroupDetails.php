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
CALL {$db->_['PGroupMembers']}('{$id}');
EOD;
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

// Fetch and use the results
// First details of the group
$row = $results[0]->fetch_object();
$id 					= $row->id;
$name 				= $row->name;
$description 	= $row->description;
$members 			= $row->members;

// Then the members
global $gModule;
$removeLink = "?m={$gModule}&amp;p=acp-groupmemremp&amp;do-submit=remove-member&amp;gid={$id}&amp;mid=";
$memberList = '';

// Change link if users can not be removed
$titleBase = $pc->lang['REMOVE_MEMBER'];
if($id == $db->_['CIdOfUserGroup']) {
	$titleBase = "%1s%2s";
}
while($row = $results[2]->fetch_object()) {
	$title = sprintf($titleBase, $row->account, (empty($row->name) ? '' : "({$row->name})"));
	$memberList .= "<a href='{$removeLink}{$row->id}' title='{$title}'>{$row->account}</a>, ";
}
$memberList = substr($memberList, 0, -2);

$results[0]->close();
$results[2]->close();
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
$action 	= "?m={$gModule}&amp;p=acp-groupdetailsp";
$redirect = "?m={$gModule}&amp;p=acp-groupdetails&amp;id={$id}";

// Link to delete group
$delGroup = "?m={$gModule}&amp;p=acp-groupdelp&amp;do-submit=del-group&amp;id={$id}";
$delGroup = <<<EOD
	<ul class='nav-standard nav-links'>
		<li><a href='{$delGroup}' title='{$pc->lang['CLICK_TO_DELETE']}'>{$pc->lang['GROUP_DELETE']}</a>
	</ul>
EOD;

// Only delete available if empty group
if($members != 0) {
	$delGroup = "<p>" . sprintf($pc->lang['NO_DELETE_HAS_MEMBERS'], $members) . "</p>";
}

// No edit/delete of system groups
$readonly = '';
$disabled = '';
if($id <= $db->_['CNrOfSystemGroups']) {
	$delGroup = "<p><em>{$pc->lang['NO_DELETE_SYSTEM_GROUP']}</em></p>";
	$readonly = "readonly='readonly'";
	$disabled = "disabled='disabled'";
}

// Show no-members message if suitable
$noMembers = ($members == 0 ? "<em>{$pc->lang['NO_MEMBERS']}</em>" : '');

// No delete of accounts from the user-group
$noRemove = '';
if($id == $db->_['CIdOfUserGroup']) {
	$noRemove = "<p><em>{$pc->lang['NO_REMOVE_USER_GROUP']}</em></p>";
}

// Get and format messages from session if they are set
$helpers = new CHTMLHelpers();
$messages = $helpers->GetHTMLForSessionMessages(
	Array('successDetails', 'successMembers'), 
	Array('failedDetails', 'failedMembers'));

$htmlMain = <<<EOD
{$htmlCp}

<div id='sdetails' class='section'>
	<form action='{$action}' method='post'>
		<input type='hidden' name='redirect' 				value='{$redirect}'>
		<input type='hidden' name='redirect-fail' 	value='{$redirect}'>
		<input type='hidden' name='id' 							value='{$id}'>
		
		<fieldset class='standard type-2'>
	 		<legend>{$pc->lang['GROUP_DETAILS_LEGEND']}</legend>
		 	<div class='form-wrapper'>

				<label for="name">{$pc->lang['GROUP_DETAILS_NAME']}</label>
				<input {$readonly} name='name' type='text' value='{$name}' maxlength='{$db->_['CSizeGroupName']}'>

				<label for='description'>{$pc->lang['GROUP_DETAILS_DESCRIPTION']}</label>
				<textarea {$readonly} name='description' rows='2' maxlength='{$db->_['CSizeGroupDescription']}'>{$description}</textarea>

				<div class='buttonbar'>
					<button {$disabled} type='submit' class='save' name='do-submit' value='save-group'>{$pc->lang['GROUP_SAVE']}</button>
				</div> <!-- buttonbar -->

				<div class='form-status'>{$messages['successDetails']}{$messages['failedDetails']}</div> 
		 </div> <!-- wrapper -->

		{$delGroup}
		
		</fieldset>
	</form>
</div> <!-- section -->


<div id='smembers' class='section'>
	<form action='{$action}' method='post'>
		<input type='hidden' name='redirect' 				value='{$redirect}#smembers'>
		<input type='hidden' name='redirect-fail' 	value='{$redirect}#smembers'>
		<input type='hidden' name='id' 							value='{$id}'>
		
		<fieldset class='standard type-3'>
	 		<legend>{$pc->lang['GROUP_MEMBERS_LEGEND']}</legend>
		 	<div class='form-wrapper'>

				<p>{$pc->lang['GROUP_MEMBERS_TITLE']}<br>{$memberList}{$noMembers}</p>
				
				<label for="prospects">{$pc->lang['ADD_MEMBERS_BY_ACCOUNT']}</label>
				<input name='prospects' type='text'>

				<div class='buttonbar'>
					<button type='submit' class='add' name='do-submit' value='add-members'>{$pc->lang['GROUP_MEMBERS_ADD']}</button>
				</div> <!-- buttonbar -->

				<div class='form-status'>{$messages['successMembers']}</div> 
				<div class='form-status'>{$messages['failedMembers']}</div> 
		 </div> <!-- wrapper -->
		 
		 {$noRemove}
		 
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