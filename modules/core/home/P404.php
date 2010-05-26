<?php
// ===========================================================================================
//
// File: P404.php
//
// Description: Generate a 404 header and print message, could also logg into database.
//
// Author: Mikael Roos, mos@bth.se
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


// -------------------------------------------------------------------------------------------
//
// Page specific code
//

$htmlMain = <<<EOD
<h1>{$pc->lang['404_HEADER']}</h1>
<p>{$pc->lang['404_DESCRIPTION']}</p>

EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

header("HTTP/1.0 404 Not Found");
$page->printPage($pc->lang['404_TITLE'], "", $htmlMain, "");
exit;

?>