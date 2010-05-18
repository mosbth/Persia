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


// -------------------------------------------------------------------------------------------
//
// Page specific code
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
This is a standard forms <code>&lt;input type='file'&gt;</code> kind of file upload.
</p>

<form enctype="multipart/form-data" action="{$action}" method="post">
<fieldset class='standard'>
<legend>Single file upload</legend>
<input type="hidden" name="MAX_FILE_SIZE" value="{$maxFileSize}">
<label for='file'>File to upload:</label>
<input name='file' type='file'>
<button type='submit' name='submit' value='single-by-traditional-form'>Upload</button>
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
<button type='submit' name='submit' value='multiple-by-traditional-form'>Upload</button>
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

$page->PrintPage("File upload", $htmlLeft, $htmlMain, $htmlRight);
exit;

?>