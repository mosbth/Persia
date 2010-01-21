<?php
// ===========================================================================================
//
// PInstallProcess.php
//
// Executes SQL statments in database, displays the results.
//
// Author: Mikael Roos
//


// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
$pc = new CPageController();
//$pc->LoadLanguage(__FILE__);


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
$query 	= $db->LoadSQL('SQLCreateUserAndGroupTables.php');
$res 	= $db->MultiQuery($query); 
$no		= $db->RetrieveAndIgnoreResultsFromMultiQuery();


// -------------------------------------------------------------------------------------------
//
// Prepare the text
//
$htmlMain = <<< EOD
<h1>Database installed</h1>
<p>
SQL Query was:
<div class="sourcecode">
<pre>{$query}</pre>
</div>
</p>
<p>Statements that succeeded: {$no}</p>
<p>Error code: {$mysqli->errno} ({$mysqli->error})</p>
EOD;

$htmlLeft 	= "";
$htmlRight	= "";


// -------------------------------------------------------------------------------------------
//
// Close the connection to the database
//
$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->printPage('Database installed', $htmlLeft, $htmlMain, $htmlRight);
exit;

?>