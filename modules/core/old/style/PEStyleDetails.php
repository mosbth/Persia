<?php
// ===========================================================================================
//
// Extension of a pagecontroller (PE)
// PEStyleDetails.php
//
// Specific settings page.
// Show details about an account.
//
// To be included from a pagecontroller that must have the following defined:
//
// $pc
// $infFilter
// $htmlTitle
// $htmlLeft
// $htmlMain
// $htmlRight
//

// -------------------------------------------------------------------------------------------
//
// A pacecontroller has included this file, $pc & $intFilter is defined
//
$pc->LoadLanguage(__FILE__);
$intFilter->frontcontrollerIsVisitedOrDie();


// -------------------------------------------------------------------------------------------
//
// Page specific code
//
$htmlTitle = $pc->lang['STYLE_DETAILS'];

$headerMenu = $pc->GetSidebarMenu(unserialize(MENU_STYLEBAR));

$htmlMain = <<<EOD
<div id='settings'>
<div id='scard'>
<h2>{$pc->lang['STYLE']}</h2>
</div>
<div id='smenu'>
{$headerMenu}
</div>

EOD;

$htmlRight = "";

$htmlTitle = "";

// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database.
//
$mysqli = $pc->ConnectToDatabase();


// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//
$query = "";

$user = $_SESSION['accountUser'];

require_once(TP_SQLPATH . "SUserDetails.php");

$res = $pc->Query($query);


// -------------------------------------------------------------------------------------------
//
// Show the results of the query
//
$row = $res->fetch_object();

$htmlMain .= <<< EOD
<div id='swrap'>
<div class='sblock'>
<div class='stitle'>
<p>
{$pc->lang['CHOOSEN_STYLE:']}
</p>
</div>
<div class='scontent'>
<p>
{$row->accountUser} (id={$row->idUser})
</p>
</div>
</div> <!-- End of sblock -->


</div> <!-- End of #wrap -->
</div> <!-- End of #settings -->
EOD;


// -------------------------------------------------------------------------------------------
//
// Use the results of the query 
//

$res->close();


// -------------------------------------------------------------------------------------------
//
// Close the connection to the database
//
$mysqli->close();


?>