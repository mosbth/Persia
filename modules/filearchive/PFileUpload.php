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
//$articleId	= $pc->GETisSetOrSetDefault('article-id', 0);
//$userId		= $_SESSION['idUser'];

// Always check whats coming in...
//$pc->IsNumericOrDie($articleId, 0);

// Link to images
$imageLink = WS_IMAGES;


// -------------------------------------------------------------------------------------------
//
// Add JavaScript and html head stuff related to JavaScript
//
$js = WS_JAVASCRIPT;
$needjQuery = TRUE;
$htmlHead = <<<EOD
<!-- jGrowl latest -->
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
		// return a datatype of json
		dataType: 'json',
		
		// remove short delay before posting form when uploading files
		//forceSync: true,
		
		// form should always target the server response to an iframe. This is useful in conjuction with file uploads.
		// iframe: true,
		
		// do stuff before submitting form
		beforeSubmit: function(data, status) {
						$.jGrowl('Before submit...');
						$('#status1').html(loader);
				},
				
		// define a callback function
		success: function(data, status) {
						$.jGrowl("Uploaded file '" + data.name + "' with size=" + data.size + " as " + data.type + ".");
						$('#debug1').html(print_r(data, true));
						if(data.success == 1) {
							$('#status1').html("Uploaded file '" + data.name + "' with size=" + data.size + " as " + data.type + ".");
						} else {
							$('#status1').html("Failed to upload file. Error code = " + data.error);						
						}
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
$redirect 		= "?m={$gModule}&amp;p=upload";
$redirectFail = "?m={$gModule}&amp;p=upload";

// Get and format messages from session if they are set
$helpers = new CHTMLHelpers();
$messages = $helpers->GetHTMLForSessionMessages(
	Array(), 
	Array('createAccountFailed'));

$htmlMain = <<<EOD
<h1>Sample file uploads</h1>
<p>
Each file you upload will be visible in the 'Archive'.
</p>

<p>
This is a Ajax-enabled form for file upload. It uses 
</p>

<form id='form1' enctype="multipart/form-data" action="{$action}" method="post">
<fieldset class='standard'>
<legend>Ajax-enabled file upload</legend>
<input type="hidden" name="MAX_FILE_SIZE" value="{$maxFileSize}">
<label for='file'>File to upload:</label>
<input name='file' type='file'>
<button type='submit' name='do-submit' value='ajax-enabled'>Upload</button>
<span id='status1'>&nbsp;</span>
<pre id='debug1' style='border: 1px dotted black; background: white;'>&nbsp;</pre>
</fieldset>
</form>

<p>
This is a standard forms <code>&lt;input type='file'&gt;</code> kind of file upload.
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

<p>
Standard forms using multiple file upload.
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