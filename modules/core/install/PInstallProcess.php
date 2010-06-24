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
// File: PInstallProcess.php
//
// Description: Executes SQL statments in database, displays the results.
//
// Author: Mikael Roos, mos@bth.se
//
// Known issues:
// -
//
// History: 
// 2010-06-21: Updated structure with controllers.
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


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
// Always check whats coming in...
//


// -------------------------------------------------------------------------------------------
//
// Execute several queries and print out the result.
//
$mysqli = $db->Connect();

// Standard modules
$queries = Array(
	'SQLCoreAccount.php', 
	'SQLCoreArticle.php', 
	'SQLCoreFile.php', 
	'SQLCoreCreateDefaultData.php', 
	'SQLForumRomanum.php',
);

// Optional modules
global $gModulesAvailable;
if(isset($gModulesAvailable['dada'])) {
	$queries[] = $gModulesAvailable['dada'] . '/sql/SQLCreate.php';
	$queries[] = $gModulesAvailable['dada'] . '/sql/SQLInstall.php';
}

$status = Array();

$htmlSQL = "";
foreach($queries as $val) {

	$query 	= $db->LoadSQL($val);
	$res 		= $db->MultiQuery($query); 
	$no			= $db->RetrieveAndIgnoreResultsFromMultiQuery();
	$title 			= sprintf($pc->lang['SQL_QUERY'], $val);
	$statements	= sprintf($pc->lang['STATEMENTS_SUCCEEDED'], $no);
	$errorcode	= sprintf($pc->lang['ERROR_CODE'], $mysqli->errno, $mysqli->error);

	$status[$val] = Array('statements' => $no, 'error' => $mysqli->errno);	

	$htmlSQL .= <<< EOD
<h3>{$title}'</h3>
<p>
<div class="sourcecode height40em">
<pre>{$query}</pre>
</div>
</p>
<p>{$statements}</p>
<p>{$errorcode}</p>
EOD;
}

$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Prepare the text
//
$htmlStatus = "<ul>";
foreach($status as $key => $val) {
	$htmlStatus .= <<<EOD
<li>{$key}: Statements succeded={$status[$key]['statements']}, error code={$status[$key]['error']}
EOD;
}
$htmlStatus .= "</ul>";

$htmlMain = <<< EOD
<h1>{$pc->lang['DATABASE_INSTALLATION']}</h1>
{$htmlStatus}
{$htmlSQL}
EOD;

$htmlLeft 	= "";
$htmlRight	= "";


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
CHTMLPage::GetInstance()->printPage($pc->lang['DATABASE_INSTALLATION_LOG'], $htmlLeft, $htmlMain, $htmlRight);
exit;


?>