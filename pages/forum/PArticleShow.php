<?php
// ===========================================================================================
//
// PArticleShow.php
//
// Show the content of an article
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
//$intFilter->UserIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$articleId	= $pc->GETisSetOrSetDefault('article-id', 0);
$userId		= $_SESSION['idUser'];

// Always check whats coming in...
$pc->IsNumericOrDie($articleId, 0);


// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database, get the query and execute it.
// Relates to files in directory TP_SQLPATH.
//
$db 	= new CDatabaseController();
$mysqli = $db->Connect();

$query = <<< EOD
CALL PGetArticleDetailsAndArticleList({$articleId}, '{$userId}');
EOD;

// Perform the query
$results = Array();
$res = $db->MultiQuery($query); 
$db->RetrieveAndStoreResultsFromMultiQuery($results);
	
// Get article details
$row = $results[0]->fetch_object();
$title 		= $row->title;
$content 	= $row->content;
$saved	 	= $row->latest;
$username 	= $row->username;
$results[0]->close(); 

// Get the list of articles
$list = "";
while($row = $results[1]->fetch_object()) {    
	$list .= "<a title='{$row->info}' href='?p=article-show&amp;article-id={$row->id}'>{$row->title}</a><br>";
}
$results[1]->close(); 

$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Page specific code
//
$htmlMain = <<<EOD
<article class="general">
<h1 class="nostyle">{$title}</h1>
<p>{$content}</p>
<p class="notice">
Created by {$username}. Updated: {$saved}
</p>
</article>

EOD;

$htmlLeft 	= "";
$htmlRight	= <<<EOD
<h3 class='columnMenu'>Actions</h3>
<p>
<a href="?p=article-edit&amp;article-id={$articleId}">Edit this article...</a>
<br>
<a href="?p=article-edit">Create new article...</a>
</p>
<h3 class='columnMenu'>My latest articles</h3>
<p>
{$list} 
</p>
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->printPage("Article: {$title}", $htmlLeft, $htmlMain, $htmlRight);
exit;

?>