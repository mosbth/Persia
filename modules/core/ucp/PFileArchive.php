<?php
// ===========================================================================================
//
// File: PFileArchive.php
//
// Description: Show the content of the users filearchive which contains all uploaded files.
//
//
// Author: Mikael Roos, mos@bth.se
//


// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
$pc = CPageController::GetInstance();
$pc->LoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// User controller, get info about the current user
//
$uc 		= CUserController::GetInstance();
$userId	= $uc->GetAccountId();


// -------------------------------------------------------------------------------------------
//
// Interception Filter, controlling access, authorithy and other checks.
//
$intFilter = CInterceptionFilter::GetInstance();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();
$intFilter->UserIsCurrentUserOrMemberOfGroupAdminOr403($userId);


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
// Always check whats coming in...
// 


// -------------------------------------------------------------------------------------------
//
// Get content of file archive from database
//
$db = CDatabaseController::GetInstance();
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
// Include the menu-bar for the User Control Panel.
//
$htmlMenuBar = "";
include(dirname(__FILE__) . '/IUserControlPanel.php');


// -------------------------------------------------------------------------------------------
//
// Create HTML for page
//
$htmlMain = <<< EOD
{$htmlMenuBar}
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
$page = new CHTMLPage();

$page->PrintPage(sprintf($pc->lang['FILEARCHIVE_FOR'], $uc->GetAccountName()), $htmlLeft, $htmlMain, $htmlRight);
exit;


?>