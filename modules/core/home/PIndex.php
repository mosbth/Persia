<?php
// ===========================================================================================
//
//    Persia (http://phpersia.org), software to build web applications.
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
// File: PIndex.php
//
// Description:
// A default home page. 
//
// Author: Mikael Roos, mos@bth.se
//
// Known issues:
// -
//
// History: 
// 2010-08-10: New structure for instantiating controllers. Included license message.
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


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
// Always check whats coming in...
//


// -------------------------------------------------------------------------------------------
//
// Create HTML for the page.
//



$htmlMain = <<<EOD
EOD;


$htmlMain = <<<EOD
<div class='section'>
	<h1>Welcome</h1>
	<p>
	This is the index-page (page/home/PIndex.php). Change it to get going. Review the PTemplate.php
	(page/home/PTemplate.php) for a more complete pagecontroller.
	</p>
</div> <!-- section -->
EOD;

$htmlLeft = "";
$htmlRight = "";


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
CHTMLPage::GetInstance()->printPage('Template', $htmlLeft, $htmlMain, $htmlRight);
exit;


?>