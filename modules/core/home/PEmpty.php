<?php
// ===========================================================================================
//
//    Persia (http://phpersia.org), software to build webbapplications.
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
// File: PEmpty.php
//
// Description:
// An empty page. Usefull for quickly setting up a menu structure for the site.
//
// Author: Mikael Roos, mos@bth.se
//
// Known issues:
// -
//
// History: 
// 2010-06-18: Created.
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
//$pc = CPageController::GetInstanceAndLoadLanguage(__FILE__);
$pc = CPageController::GetInstance();
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
//$pc->IsNumericOrDie($item, 0, 2);


// -------------------------------------------------------------------------------------------
//
// Create HTML for the page.
//
global $gModule, $gPage;

$linkToThisPage = $pc->CurrentURL();

$htmlMain = <<<EOD
<div class='section'>
	<h1>This is an empty page</h1>
	<p>
	The link to this page is:
		<blockquote class='links'>
			<a href='{$linkToThisPage}'>{$linkToThisPage}</a>
		</blockquote>
	</p>
</div> <!-- section -->

EOD;

$htmlLeft = "";
$htmlRight = "";


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
CHTMLPage::GetInstance()->printPage('Empty page', $htmlLeft, $htmlMain, $htmlRight);
exit;


?>