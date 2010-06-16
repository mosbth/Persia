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
// File: PFileUpload.php
//
// Description:
// Pagecontroller to upload a file to the users filearchive.
//
// Author: Mikael Roos, mos@bth.se
//
// Known issues:
// Consider removing dependency on jquery_form-plugin and even jquery.
//
// History: 
// 2010-06-16: Moved to ucp and rewritten for first "official" use.
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
$if->UserIsSignedInOrRedirectToSignIn();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
// Always check whats coming in...
//


// -------------------------------------------------------------------------------------------
//
// Add JavaScript and html head, all related to JavaScript
// Used in printPage:
//  $page->PrintPage($title, $htmlLeft, $htmlMain, $htmlRight, $htmlHead, $javaScript, $needjQuery); 
//
$js = WS_JAVASCRIPT;
$imageLink = WS_IMAGES;
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
	var loader = new Image();
	loader.src = "{$imageLink}/loader.gif";
	loader.align = "baseline";


	// ----------------------------------------------------------------------------------------------
	//
	// Upgrade form to make Ajax submit
	//
	// http://malsup.com/jquery/form/
	//
	$('#form1').ajaxForm({
		// do stuff before submitting form
		beforeSubmit: function(data, status) {
						$('#status1').html(loader);
				},
				
		// define a callback function
		success: function(data, status) {
						$('#status1').html(data);
				},
		});
	});

EOD;


// -------------------------------------------------------------------------------------------
//
// UCP. Include the menu-bar for the User Control Panel.
//
$htmlUcp = "";
require(dirname(__FILE__) . '/IUserControlPanel.php');


// -------------------------------------------------------------------------------------------
//
// Create HTML for page
//
global $gModule;

$action 			= "?m={$gModule}&amp;p=uploadp";
$maxFileSize 	= FILE_MAX_SIZE;
$htmlLimitations = sprintf($pc->lang['FILEUPLOAD_LIMITATIONS'], round(FILE_MAX_SIZE/1000000, 2)); 

$htmlMain = <<< EOD
{$htmlUcp}
<div class='section'>
	<p>{$pc->lang['FILEUPLOAD_DESCRIPTION']}</p>
</div> <!-- section -->

<div class='section'>
	<form id='form1' enctype="multipart/form-data" action="{$action}" method="post">
		<input type="hidden" name="MAX_FILE_SIZE" value="{$maxFileSize}">
		<fieldset class='standard type-1 fileupload-standard'>
			<legend>{$pc->lang['FILEUPLOAD_LEGEND']}</legend>
		 	<div class='form-wrapper'>
				<p>{$htmlLimitations}</p>
				<label for='file'>{$pc->lang['FILEUPLOAD_LABEL']}</label>
				<input name='file' type='file'>
				<button type='submit' name='do-fileupload' value='upload-return-html'>Upload</button>
				<div class='form-status'><span id='status1'></span></div> 
			</div> <!-- wrapper -->
		</fieldset>
	</form>
</div> <!-- section -->

EOD;

$htmlLeft 	= "";
$htmlRight	= <<<EOD
<!--
<h3 class='columnMenu'>Tags</h3>
<p>
Later...
</p>
-->
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
CHTMLPage::GetInstance()->printPage(sprintf($pc->lang['FILEUPLOAD_FOR'], $uc->GetAccountName()), $htmlLeft, $htmlMain, $htmlRight, $htmlHead, $javaScript, $needjQuery);
exit;


?>