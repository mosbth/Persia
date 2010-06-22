<?php
// ===========================================================================================
//
// PInstall.php
//
// Info page for installation. Links to page for creating tables in the database.
//
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
// File: PInstall.php
//
// Description: Info page for installation. Links to page for creating tables in the database.
//
// Author: Mikael Roos, mos@bth.se
//
// Known issues:
// -
//
// History: 
// 2010-06-21: Language support.
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


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
// Always check whats coming in...
//


// -------------------------------------------------------------------------------------------
//
// Page specific code
//
global $gModule;
require_once(TP_SQLPATH . 'config.php');

$link			= sprintf($pc->lang['INSTALL_PROCESS_LINK'], "?m={$gModule}&amp;p=installp");
$host			= sprintf($pc->lang['INSTALL_DATABASE_HOST'], DB_HOST);
$database	= sprintf($pc->lang['INSTALL_DATABASE_NAME'], DB_DATABASE);
$prefix		= sprintf($pc->lang['INSTALL_DATABASE_PREFIX'], DB_PREFIX);

$htmlMain = <<<EOD
<div class='section'>
	<h1>{$pc->lang['INSTALL_HEADER']}</h1>
	<p>{$pc->lang['INSTALL_DESCRIPTION']}</p>
	<p>{$host}</p>
	<p>{$database}</p>
	<p>{$prefix}</p>
	<p>{$pc->lang['INSTALL_CHANGE_CONFIG']}</p>
	<p>{$link}</p>
</div> <!-- section -->

EOD;

$htmlLeft 	= "";
$htmlRight 	= "";


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
CHTMLPage::GetInstance()->printPage($pc->lang['INSTALL_PAGE_TITLE'], $htmlLeft, $htmlMain, $htmlRight);
exit;

 
?>