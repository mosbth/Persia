<?php
// ===========================================================================================
//
// File: PInstallProcess.php
//
// Description: Executes SQL statments in database, displays the results.
//
// Author: Mikael Roos
//


// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
$pc = new CPageController();
$pc->LoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// Interception Filter, controlling access, authorithy and other checks.
//
$intFilter = new CInterceptionFilter();

$intFilter->FrontControllerIsVisitedOrDie();
//$intFilter->UserIsSignedInOrRecirectToSignIn();
//$intFilter->UserIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database, get the query and execute it.
//
$db 	= new CDatabaseController();
$mysqli = $db->Connect();


// -------------------------------------------------------------------------------------------
//
// Execute several queries and print out the result.
//
$queries = Array(
	'SQLCoreAccount.php', 
	'SQLCoreArticle.php', 
	'SQLCoreFile.php', 
	'SQLCoreCreateDefaultData.php', 
	'SQLForumRomanum.php',
);

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


// -------------------------------------------------------------------------------------------
//
// Close the connection to the database
//
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
$page = new CHTMLPage();

$page->printPage($pc->lang['DATABASE_INSTALLATION_LOG'], $htmlLeft, $htmlMain, $htmlRight);
exit;

?>