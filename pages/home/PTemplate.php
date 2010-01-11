<?php
// ===========================================================================================
//
// PTemplate.php
//
// A standard template page for a pagecontroller.
//
// This is an example on how to use/create a pagecontroller. It shows the features available 
// from a pagecontroller.
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

$intFilter->frontcontrollerIsVisitedOrDie();
//$intFilter->userIsSignedInOrRecirectToSign_in();
//$intFilter->userIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Page specific code
//

$htmlMain = <<<EOD
<h1>Template</h1>
<h2>Introduktion</h2>
<p>
Kopiera denna template sida för att skapa nya pagecontrollers. 
</p>
<p>
<a href='?p=ls&amp;dir=pages/home&amp;file=PTemplate.php'>Källkoden till sidan ser du här</a>.
</p>
<p>
{$pc->lang['TEXT1']}
</p>
EOD;

$htmlLeft = <<<EOD
<h3 class='columnMenu'>Bra att ha vänster</h3>
<p>
Här finns nu en meny kolumn som går att använda till bra att ha saker.
</p>
<p>
{$pc->lang['TEXT2']}
</p>
EOD;

$htmlRight = <<<EOD
<h3 class='columnMenu'>Bra att ha höger</h3>
<p>
Här finns nu en meny kolumn som går att använda till bra att ha saker.
</p>
<p>
{$pc->lang['TEXT3']}
</p>
EOD;


// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database.
//
/*
$mysqli = $pc->ConnectToDatabase();
*/


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
/*
$user 		= isset($_POST['nameUser']) ? $_POST['nameUser'] : '';
$password 	= isset($_POST['passwordUser']) ? $_POST['passwordUser'] : '';

// Prevent SQL injections
$user 		= $mysqli->real_escape_string($user);
$password 	= $mysqli->real_escape_string($password);
*/


// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//
/*
$tableTable	= DB_PREFIX . 'Table';

$query = <<< EOD
;
EOD;

$res = $mysqli->query($query) 
                    or die("<p>Could not query database,</p><code>{$query}</code>");
*/


// -------------------------------------------------------------------------------------------
//
// Use the results of the query 
//
/*
$res->close();
*/

// -------------------------------------------------------------------------------------------
//
// Close the connection to the database
//
/*
$mysqli->close();
*/


// -------------------------------------------------------------------------------------------
//
// Redirect to another page
// Support $redirect to be local uri within site or external site (starting with http://)
//
/*
require_once(TP_SOURCEPATH . 'CHTMLPage.php');

$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'home';

CHTMLPage::redirectTo($redirect);
exit;
*/

// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->printPage('Template', $htmlLeft, $htmlMain, $htmlRight);
exit;

?>