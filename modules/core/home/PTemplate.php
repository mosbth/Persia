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
// File: PTemplate.php
//
// Description:
// A standard template page for a pagecontroller. This is an example on how to use/create a 
// pagecontroller. It shows (should) the features available.
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
//$if->UserIsSignedInOrRedirectToSignIn();
//$if->UserIsCurrentUserOrMemberOfGroupAdminOr403($uc->GetAccountId());
//$if->UserIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
//$var = $pc->GETisSetOrSetDefault('item', '1');
//$var = $pc->POSTisSetOrSetDefault('item', '1');
//$var = $pc->SESSIONisSetOrSetDefault('item', '1');
//$userId	= $uc->GetAccountId();
//
// Always check whats coming in...
//strip_tags($item)
//trim($account);
//if(preg_replace('/[a-zA-Z0-9]/', '', $account)) {
//$pc->IsNumericOrDie($item, 0, 2);
//$account = $mysqli->real_escape_string($account);

// To show off the template and display the flexible 1-2-3 column layout used.
$showLeft 	= $pc->GETisSetOrSetDefault('showLeft', '1');
$showRight 	= $pc->GETisSetOrSetDefault('showRight', '1');

$pc->IsNumericOrDie($showLeft, 0, 2);
$pc->IsNumericOrDie($showRight, 0, 2);


/*
// -------------------------------------------------------------------------------------------
//
// Add JavaScript and html head, all related to JavaScript
// Used in printPage:
//  $page->PrintPage($title, $htmlLeft, $htmlMain, $htmlRight, $htmlHead, $javaScript, $needjQuery); 
//
$js = WS_JAVASCRIPT;
$imageLink = WS_IMAGES;
$needjQuery = TRUE;

$htmlHead = <<<EOD
<!-- jQuery Form Plugin -->
<script type='text/javascript' src='{$js}/form/jquery.form.js'></script>  
EOD;

$javaScript = <<<EOD
// ----------------------------------------------------------------------------------------------
//
// Initiate JavaScript when document is loaded.
//
$(document).ready(function() {

	// Preload loader image
	var loader = new Image();
	loader.src = "{$imageLink}/loader.gif";
	loader.align = "baseline";


	// ----------------------------------------------------------------------------------------------
	//
	// Upgrade form to make Ajax submit
	//
	// http://malsup.com/jquery/form/
	//
	$('#form1').ajaxForm({
		// do stuff before submitting form
		beforeSubmit: function(data, status) {
						$('#status1').html(loader);
				},
				
		// define a callback function
		success: function(data, status) {
						$('#status1').html(data);
				},
		});
	});

EOD;

*/


// -------------------------------------------------------------------------------------------
//
// About gModule and gPage
//
global $gModule, $gPage;

$linkToThisPage = $pc->CurrentURL();

$htmlModuleAndPage = <<<EOD
<div id='links' class='section'>
	<h3>About linking, \$gModule and \$gPage</h3>
	<p>
		The global variable \$gModule states which modulecontroller currently used. The global variable
		\$gPage states the current pagecontroller. Current values are:
		<code class='standard-box'>
			\$gModule = '{$gModule}'<br />
			\$gPage = '{$gPage}'<br />
		</code>
	</p>
	<p>
		You can create a link to the current page using the following method:
		<code class='standard-box'>
			CPageController::CurrentURL();
		</code> 
		The link to this page would then be:
		<blockquote class='links'>
			<a href='{$linkToThisPage}'>{$linkToThisPage}</a>
		</blockquote>
	</p>
</div> <!-- section -->
EOD;


// -------------------------------------------------------------------------------------------
//
// Create HTML for the page.
//
$htmlMain = <<<EOD
<h1>Template</h1>
<h2>Introduction</h2>
<p>
Copy this file, PTemplate.php, to create new pacecontrollers.
</p>
<p>
<a href='?p=ls&amp;dir=modules/core/home&amp;file=PTemplate.php'>Review sourcecode for PTemplate.php</a>.
</p>
<p>
{$pc->lang['TEXT1']}
</p>
<p>
<a href='?p=template&amp;showLeft=1&amp;showRight=1'>Show all 3 columns</a>
</p>

{$htmlModuleAndPage}

EOD;

$htmlLeft = <<<EOD
<h3 class='columnMenu'>Left column</h3>
<p>
This is HTML for the left column. Use it or loose it. 
</p>
<p>
{$pc->lang['TEXT2']}
</p>
<p>
<a href='?p=template&amp;showLeft=2&amp;showRight={$showRight}'>Do not display this column</a>
</p>
EOD;

$htmlRight = <<<EOD
<h3 class='columnMenu'>Right column</h3>
<p>
This is HTML for the right column. Use it or loose it. 
</p>
<p>
{$pc->lang['TEXT3']}
</p>
<p>
<a href='?p=template&amp;showLeft={$showLeft}&amp;showRight=2'>Do not display this column</a>
</p>
EOD;

// Display only thos column thats choosen
$htmlLeft 	= (($showLeft == 1) ? $htmlLeft : "");
$htmlRight 	= (($showRight == 1) ? $htmlRight : "");


// -------------------------------------------------------------------------------------------
//
// Connect to the database, create the query and execute it, take care of the results.
//
/*
// Connect to the database
$mysqli = $db->Connect();
*/

/*
// Create query and execute it

// Multiquery from file
$query 	= $db->LoadSQL('SQLCreateUserAndGroupTables.php');
$res 	= $db->MultiQuery($query); 
$no		= $db->RetrieveAndIgnoreResultsFromMultiQuery();

// Single query from file
$query 	= $db->LoadSQL('SQLLoginUser.php');
$res 	= $db->Query($query); 
*/

// Use the results of the query 

/*
// Close database resources
$res->close();
$mysqli->close();
*/


// -------------------------------------------------------------------------------------------
//
// Redirect to another page
// Support $redirect to be local uri within site or external site (starting with http://)
//
//CPageController::RedirectTo(CPageController::POSTisSetOrSetDefault('redirect', 'home'));
//$pc->RedirectTo($pc->POSTisSetOrSetDefault('redirect', 'home'));
//$pc->RedirectToModuleAndPage('', 'p403', '', $db->_['FFileCheckPermissionMessages'][$row->success]);
//exit;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
// OBSOLETE
//$page = new CHTMLPage();  
//$page->printPage($title, $htmlLeft, $htmlMain, $htmlRight);
//$page->PrintPage($title, $htmlLeft, $htmlMain, $htmlRight, $htmlHead, $javaScript, $needjQuery); 
//
// DO AS THIS
//CHTMLPage::GetInstance()->printPage($title, $htmlLeft, $htmlMain, $htmlRight);
//CHTMLPage::GetInstance()->PrintPage($title, $htmlLeft, $htmlMain, $htmlRight, $htmlHead, $javaScript, $needjQuery); 
//
CHTMLPage::GetInstance()->printPage('Template', $htmlLeft, $htmlMain, $htmlRight);
exit;


?>