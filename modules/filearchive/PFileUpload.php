<?php
// ===========================================================================================
//
// File: PFileUpload.php
//
// Description: Various samples of uploading files.
//
// Author: Mikael Roos, mos@bth.se
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
$intFilter->UserIsSignedInOrRecirectToSignIn();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//

// Link to images
$imageLink = WS_IMAGES;


// -------------------------------------------------------------------------------------------
//
// Add JavaScript and html head stuff related to JavaScript
//
$js = WS_JAVASCRIPT;
$needjQuery = TRUE;
$htmlHead = <<<EOD
<!-- jGrowl notices -->
<link rel='stylesheet' href='{$js}/jGrowl/jquery.jgrowl.css' type='text/css' />
<script type='text/javascript' src='{$js}/jGrowl/jquery.jgrowl.js'></script>  

<!-- jQuery Form Plugin -->
<script type='text/javascript' src='{$js}/form/jquery.form.js'></script>  

<!-- PHPJS, PHP in JavaScript -->
<script type='text/javascript' src='{$js}/phpjs/debug.js'></script>  

EOD;

$javaScript = <<<EOD

// ----------------------------------------------------------------------------------------------
//
// Initiate JavaScript when document is loaded.
//
$(document).ready(function() {

	// Just showing off jGrowl to see that it works
	$.jGrowl("Hello World. This is Growl. Page is now loaded and ready for some action.");

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
		// $.ajax options can be used here too, for example: 
		//timeout: 1000, 

		// return a datatype of json
		//dataType: 'json',
		
		// remove short delay before posting form when uploading files
		//forceSync: true,
		
		// form should always target the server response to an iframe. This is useful in conjuction with file uploads.
		//iframe: true,
		
		// do stuff before submitting form
		beforeSubmit: function(data, status) {
						$.jGrowl('Before submit...');
						$('#status1').html(loader);
						//$('#debug1').html('');
				},
				
		// define a callback function
		success: function(data, status) {
						$.jGrowl("Uploaded file. Done.");
						$('#status1').html(data);
						//$('#debug1').html(print_r(data, true));
				}	
		});
	});

EOD;


// -------------------------------------------------------------------------------------------
//
// Create the HTML
//
global $gModule;

$maxFileSize 	= FILE_MAX_SIZE;
$action 			= "?m={$gModule}&amp;p=uploadp";
//$redirect 		= "?m={$gModule}&amp;p=upload";
//$redirectFail = "?m={$gModule}&amp;p=upload";

// Get and format messages from session if they are set
/*
$helpers = new CHTMLHelpers();
$messages = $helpers->GetHTMLForSessionMessages(
	Array(), 
	Array());
*/

$htmlMain = <<<EOD
<div class='section'>
<h1>Sample file uploads</h1>
<p>
Each file you upload will be visible in the 'Archive'.
</p>
</div>

<div class='section'>
<p>
This is a Ajax-enabled form for file upload. It uses jQuery form plugin as described here: 
<a href='http://jquery.malsup.com/form/#file-upload'>http://jquery.malsup.com/form/#file-upload</a>.
</p>

<form id='form1' enctype="multipart/form-data" action="{$action}" method="post">
<fieldset class='standard'>
<legend>Ajax-style file upload</legend>
<input type="hidden" name="MAX_FILE_SIZE" value="{$maxFileSize}">
<label for='file'>File to upload:</label>
<input name='file' type='file'>
<div id='clear'>&nbsp;</div>
<button id='submit-ajax' type='submit' name='do-submit' value='upload-return-html'>Upload</button>
<span id='status1'></span>
</div>

<!--
<p>Debug output using print_r()</p>
<pre id='debug1' style='border: 1px dotted black; background: white;'></pre>
-->

</fieldset>
</form>

<div class='section'>
<p>
This is a standard forms <code>&lt;input type='file' name='file'&gt;</code> kind of file upload.
</p>

<form enctype="multipart/form-data" action="{$action}" method="post">
<fieldset class='standard'>
<legend>Single file upload</legend>
<input type="hidden" name="MAX_FILE_SIZE" value="{$maxFileSize}">
<label for='file'>File to upload:</label>
<input name='file' type='file'>
<button type='submit' name='do-submit' value='single-by-traditional-form'>Upload</button>
</fieldset>
</form>
</div>

<div class='section'>
<p>
Standard forms using multiple file upload (<code>&lt;input type='file' name='file[]'&gt;</code>).
</p>

<form enctype="multipart/form-data" action="{$action}" method="post">
<fieldset class='standard'>
<legend>Multiple file upload</legend>
<input type="hidden" name="MAX_FILE_SIZE" value="{$maxFileSize}">
<label>File to upload: <input name='file[]' type='file'></label>
<label>File to upload: <input name='file[]' type='file'></label>
<label>File to upload: <input name='file[]' type='file'></label>
<button type='submit' name='do-submit' value='multiple-by-traditional-form'>Upload</button>
</fieldset>
</form>
</div>

EOD;

$htmlLeft 	= "";
$htmlRight	= <<<EOD
<h3 class='columnMenu'></h3>
<p>
Later...
</p>

EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->PrintPage("File upload", $htmlLeft, $htmlMain, $htmlRight, $htmlHead, $javaScript, $needjQuery);
exit;

?>