<?php
// ===========================================================================================
//
// File: PFileArchive.php
//
// Description: Show the content of the users filearchive.
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
$account = $pc->SESSIONisSetOrSetDefault('accountUser');

// Always check whats coming in...
//$pc->IsNumericOrDie($articleId, 0);


// -------------------------------------------------------------------------------------------
//
// Open and read a directory, show its content
//
$dir = FILE_ARCHIVE_PATH . DIRECTORY_SEPARATOR . $account;

$list = Array();
if(is_dir($dir)) {
	if ($dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if($file != '.' && $file != '..') {
				$list[$file] = "{$file}";
			}
		}
	closedir($dh);
	}
}

ksort($list);

$archive = "<h2>{$account}</h2><table><tr><th>Name</th></tr>";
foreach($list as $val => $key) {
    $archive .= "<tr><td>{$key}</td></tr>";
}
$archive .= '</table>';


// -------------------------------------------------------------------------------------------
//
// Page specific code
//
$htmlMain = <<<EOD
<h1>File archive</h1>
{$archive} 

EOD;

$htmlLeft 	= "";
$htmlRight	= <<<EOD
<h3 class='columnMenu'>Tags</h3>
<p>
Later...
</p>

<!--
<h3 class='columnMenu'>Hot Tags</h3>
<p>
Later...<br>
(Complete Tag Cloud)
</p>
<h3 class='columnMenu'>Recent Activity</h3>
<p>
Later...
</p>
-->
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->PrintPage("File archive for user '{$account}'", $htmlLeft, $htmlMain, $htmlRight);
exit;

?>