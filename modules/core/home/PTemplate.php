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
$pc = CPageController::GetInstance();
$pc->LoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// User controller, get info about the current user
//
$uc 		= CUserController::GetInstance();
$userId	= $uc->GetAccountId();


// -------------------------------------------------------------------------------------------
//
// Interception Filter, controlling access, authorithy and other checks.
//
$intFilter = CInterceptionFilter::GetInstance();
$intFilter->FrontControllerIsVisitedOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
// Always check whats coming in...
// strip_tags, checker-methods in CPageController 
//

// To show off the template and display the flexible 1-2-3 column layout used.
$showLeft 	= $pc->GETisSetOrSetDefault('showLeft', '1');
$showRight 	= $pc->GETisSetOrSetDefault('showRight', '1');

// Always check whats coming in...
$pc->IsNumericOrDie($showLeft, 0, 2);
$pc->IsNumericOrDie($showRight, 0, 2);


/*
// Link to images
$imageLink = WS_IMAGES;

// -------------------------------------------------------------------------------------------
//
// Add JavaScript and html head stuff related to JavaScript
//
$js = WS_JAVASCRIPT;
$needjQuery = TRUE;
$htmlHead = <<<EOD
<!-- jQuery Form Plugin -->
<script type='text/javascript' src='{$js}/form/jquery.form.js'></script>  

EOD;

$javaScript = <<<EOD

// ----------------------------------------------------------------------------------------------
//
// Initiate JavaScript when document is loaded.
//
$(document).ready(function() {

	// Preload loader image
	var loaderImg = new Image();
	loaderImg.src = "{$imageLink}/loader.gif";
	loaderImg.align = "baseline";


	// ----------------------------------------------------------------------------------------------
	//
	// Upgrade form to make Ajax submit
	//
	// http://malsup.com/jquery/form/
	//
	$('#form1').ajaxForm({
		// $.ajax options can be used here too, for example: 
		//timeout: 1000, 

		// do stuff before submitting form
		beforeSubmit: function(data, status) {
						$('#status1').html(loaderImg);
				},
				
		// define a callback function
		success: function(data, status) {
						$('#status1').html(data);
				}	
		});
	});

EOD;
*/


// -------------------------------------------------------------------------------------------
//
// Page specific code
//

$htmlMain = <<<EOD
<h1>Template</h1>
<h2>Introduction</h2>
<p>
Copy this file, PTemplate.php, to create new pacecontrollers.
</p>
<p>
<a href='?p=ls&amp;dir=modules/core/home&amp;file=PTemplate.php'>Review sourcecode for PTemplate.php</a>.
</p>
<p>
{$pc->lang['TEXT1']}
</p>
<p>
<a href='?p=template&amp;showLeft=1&amp;showRight=1'>Show all 3 columns</a>
</p>
EOD;

$htmlLeft = <<<EOD
<h3 class='columnMenu'>Left column</h3>
<p>
This is HTML for the left column. Use it or loose it. 
</p>
<p>
{$pc->lang['TEXT2']}
</p>
<p>
<a href='?p=template&amp;showLeft=2&amp;showRight={$showRight}'>Do not display this column</a>
</p>
EOD;

$htmlRight = <<<EOD
<h3 class='columnMenu'>Right column</h3>
<p>
This is HTML for the right column. Use it or loose it. 
</p>
<p>
{$pc->lang['TEXT3']}
</p>
<p>
<a href='?p=template&amp;showLeft={$showLeft}&amp;showRight=2'>Do not display this column</a>
</p>
EOD;

// Display only thos column thats choosen
$htmlLeft 	= (($showLeft == 1) ? $htmlLeft : "");
$htmlRight 	= (($showRight == 1) ? $htmlRight : "");


// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database, get the query and execute it.
// Relates to files in directory TP_SQLPATH.
//
/*
$db 	= new CDatabaseController();
$mysqli = $db->Connect();
$query 	= $db->LoadSQL('SQLCreateUserAndGroupTables.php');
$res 	= $db->MultiQuery($query); 
$no		= $db->RetrieveAndIgnoreResultsFromMultiQuery();
*/

/*
$db 	= new CDatabaseController();
$mysqli = $db->Connect();
$query 	= $db->LoadSQL('SQLLoginUser.php');
$res 	= $db->Query($query); 
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
//CPageController::RedirectTo(CPageController::POSTisSetOrSetDefault('redirect', 'home'));
$pc->RedirectTo($pc->POSTisSetOrSetDefault('redirect', 'home'));
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