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
// File: PAbout.php
//
// Description:
// Displays information from a textfile, usually the README, LICENSE and CREDITS-files.
//
// Author: Mikael Roos, mos@bth.se
//
// Known issues:
// No language support.
//
// History: 
// 2010-07-01: Support mutiple files. New structure for instantiating controllers. 
// Included license message.
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
// Choose the file depending on gPage
//
global $gPage;

$file = '';
switch($gPage) {
	
		case 'about': {
			$title = 'Persia About';
			$description = 'This is the Persia README-file.';
			$file = file_get_contents('README');
		} break;
		
		case 'credits': {
			$title = 'Persia Credits';
			$description = 'This is the Persia CREDITS-file.';
			$file = file_get_contents('CREDITS');
		} break;
		
		case 'legal': {
			$title = 'Persia Copyright and License';
			$description = 'This is the Persia LICENSE-file.';
			$file = file_get_contents('LICENSE');
		} break;
		
		case 'privacy': {
			$title = 'Persia Privacy Police';
			$description = 'This is to be done. Write privacy policy.';
		} break;
		
		default: {
			$title = 'Undefined page';
			$description = 'Undefined page';
		} break;
}


// -------------------------------------------------------------------------------------------
//
// Create HTML for the page.
//
$htmlMain = <<<EOD
<div class='section'>
	<h1>{$title}</h1>
	<p>{$description}</p>
</div> <!-- section -->

<div class='section'>
	<p><pre>{$file}</pre></p>
</div> <!-- section -->

EOD;

$htmlLeft = "";
$htmlRight = "";


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
CHTMLPage::GetInstance()->printPage($title, $htmlLeft, $htmlMain, $htmlRight);
exit;


?>