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
// File: PAccountList.php
//
// Description: Show accounts and allow to delete, modify and add.
//
// Author: Mikael Roos, mos@bth.se
//
// Known issues:
// -
//
// History: 
// 2010-07-01: Created.
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


// -------------------------------------------------------------------------------------------
//
// Connect to the database, create the query and execute it, take care of the results.
//
$mysqli = $db->Connect();
$query = <<< EOD
CALL {$db->_['PGetAccountList']}();
EOD;
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

// Take care of results
global $gModule;
$editDetails 		= "?m={$gModule}&amp;p=acp-groupdetails&amp;id=";
$editMembership = "?m={$gModule}&amp;p=acp-groupmembers&amp;id=";

$htmlRows = '';
$i=0;
while($row = $results[0]->fetch_object()) {    
	$noMembership = sprintf($pc->lang['ACCOUNT_NO_MEMBERS'], $row->memberships);
	$htmlRows .= "<tr class='r".($i++%2+1)."'>";
	$htmlRows .= <<<EOD
<!--
<td class='center'><a href='{$editDetails}{$row->id}' title='{$pc->lang['ACCOUNT_EDIT_DETAILS']}'>{$row->account}</a></td>
-->
<td class='center'>{$row->account}</td>
<td>{$row->name}</td>
<td>{$row->email}</td>
<td class='center'>{$noMembership}</td>
<!--
<td class='center'><a href='{$editMembership}{$row->id}#smembers' title='{$pc->lang['ACCOUNT_EDIT_MEMBERS']}'>{$noMembership}</a></td>
-->
</tr>
EOD;
}

// Close it up
$results[0]->close();
$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// ACP. Include the menu-bar for the Admin Control Panel.
//
$htmlCp = "";
require(dirname(__FILE__) . '/IAdminControlPanel.php');


// -------------------------------------------------------------------------------------------
//
// Create HTML for page
//
$createAccount = "?m={$gModule}&amp;p=account-create";

// Get and format messages from session if they are set
$helpers = new CHTMLHelpers();
$messages = $helpers->GetHTMLForSessionMessages(
	Array('successDetails'), 
	Array('failedDetails'));

$htmlMain = <<< EOD
{$htmlCp}
<div class='section'>
	<p>{$pc->lang['ACCOUNT_DESCRIPTION']}</p>
</div> <!-- section -->

<div class='section'>
	<table class='standard full-width'>
		<caption></caption>
		<colgroup></colgroup>
		<thead>
			<th>{$pc->lang['USER_ACCOUNT']}</th>
			<th>{$pc->lang['USER_NAME']}</th>
			<th>{$pc->lang['USER_MAIL']}</th>
			<th>{$pc->lang['USER_MEMBERSHIP']}</th>
		</thead>
		<tbody>{$htmlRows}</tbody>
		<tfoot></tfoot>
	</table>
	
	<div class='form-status'>{$messages['successDetails']}{$messages['failedDetails']}</div> 

	<ul class='nav-standard nav-links'>
		<li><a href='{$createAccount}' title='{$pc->lang['ACCOUNT_ADD_TITLE']}'>{$pc->lang['ACCOUNT_ADD']}</a>
	</ul>

</div> <!-- section -->


EOD;

$htmlLeft 	= "";
$htmlRight	= <<<EOD
<!--
<h3 class='columnMenu'>Tags</h3>
<p>
Later...
</p>
-->
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
CHTMLPage::GetInstance()->PrintPage($pc->lang['ACCOUNT_TITLE'], $htmlLeft, $htmlMain, $htmlRight);
exit;


?>