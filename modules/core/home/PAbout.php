<?php
// ===========================================================================================
//
// PAbout.php
//
// Display information about Persia, display the README-file.
//
// Author: Mikael Roos, mos@bth.se
//


// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
$pc = CPageController::GetInstance();
//$pc->LoadLanguage(__FILE__);


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
// 


// -------------------------------------------------------------------------------------------
//
// Page specific code
//
$readme = file_get_contents('README');
 
$htmlMain = <<<EOD
EOD;

$htmlMain = <<<EOD
<h1>About Persia</h1>
<h2>README</h2>
<p>
This is the README-file.
</p>
<p>
<pre>
{$readme}
</pre>
</p>
EOD;

$htmlLeft = "";
$htmlRight = "";


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->printPage('About Persia', $htmlLeft, $htmlMain, $htmlRight);
exit;

?>