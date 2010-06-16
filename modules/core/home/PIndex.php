<?php
// ===========================================================================================
//
// PIndex.php
//
// The home-page
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

$html = <<<EOD
<h1>Welcome</h1>
<p>
This is the index-page (page/home/PIndex.php). Change it to get going. Review the PTemplate.php
(page/home/PTemplate.php) for a more complete pagecontroller.
</p>
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->printPage('Index (change this)', "", $html, "");
exit;

 
?>