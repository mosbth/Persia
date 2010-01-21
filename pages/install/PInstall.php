<?php
// ===========================================================================================
//
// PInstall.php
//
// Info page for installation. Links to page for creating tables in the database.
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
// Page specific code
//
$database 	= DB_DATABASE;
$prefix		= DB_PREFIX;

$htmlMain = <<<EOD
<h1>Install database</h1>
<p>
Click below link to remove all contents from the database and create new tables and content from
scratch.
</p>
<p>
You are currently working with the database: '{$database}'
</p>
<p>
The tables will be created using the prefix: '{$prefix}'
</p>
<p>
Update the file config.php to change the values.
</p>
<p>
&not; <a href='?p=installp'>Destroy current database and create from scratch</a>
</p>
EOD;

$htmlLeft 	= "";
$htmlRight 	= "";


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->printPage('Install database', $htmlLeft, $htmlMain, $htmlRight);
exit;

 
?>