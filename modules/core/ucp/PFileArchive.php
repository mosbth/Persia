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
// File: PFileArchive.php
//
// Description: Show the content of the users filearchive which contains all uploaded files.
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

// Create the query
$query 	= <<< EOD
CALL {$db->_['PListFiles']}('{$userId}');
EOD;

// Perform the query
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

global $gModule;
$editDetails = "?m={$gModule}&amp;p=file-details&amp;file=";

$htmlFileItems = '';
$i=0;
while($row = $results[0]->fetch_object()) {    
	$htmlFileItems .= "<tr class='r".($i++%2+1)."'>";
	$htmlFileItems .= <<<EOD
<td><a href='{$editDetails}{$row->uniquename}' title='{$pc->lang['CLICK_TO_EDIT']}'>{$row->name}</a></td>
<td>{$row->mimetype}</td>
<td>{$row->size}</td>
<td>{$row->created}</td>
<td>{$row->modified}</td>
</tr>
EOD;
}

if(empty($htmlFileItems)) {
	$htmlFilearchiveIsEmpty = "<p><em>{$pc->lang['ARCHIVE_IS_EMPTY']}</e,></p>";
}

$results[0]->close();
$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// UCP. Include the menu-bar for the User Control Panel.
//
$htmlUcp = "";
require(dirname(__FILE__) . '/IUserControlPanel.php');


// -------------------------------------------------------------------------------------------
//
// Create HTML for page
//
$htmlMain = <<< EOD
{$htmlUcp}
<div class='section'>
	<p>{$pc->lang['FILEARCHIVE_DESCRIPTION']}</p>
</div> <!-- section -->

<div class='section'>
	<table class='standard full-width'>
		<caption></caption>
		<thead>
			<th>{$pc->lang['FILE_NAME']}</th>
			<th>{$pc->lang['FILE_TYPE']}</th>
			<th>{$pc->lang['FILE_SIZE']}</th>
			<th>{$pc->lang['FILE_CREATED']}</th>
			<th>{$pc->lang['FILE_MODIFIED']}</th>
		</thead>
		<tbody>{$htmlFileItems}</tbody>
		<tfoot></tfoot>
	</table>
	{$htmlFilearchiveIsEmpty}
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
CHTMLPage::GetInstance()->PrintPage(sprintf($pc->lang['FILEARCHIVE_FOR'], $uc->GetAccountName()), $htmlLeft, $htmlMain, $htmlRight);
exit;


?>