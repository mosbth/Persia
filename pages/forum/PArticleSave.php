<?php
// ===========================================================================================
//
// PArticleSave.php
//
// Saves an article to database
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
$title		= $pc->POSTisSetOrSetDefault('title', 'No title');
$content	= $pc->POSTisSetOrSetDefault('content', 'No content');
$articleId	= $pc->POSTisSetOrSetDefault('article_id', 0);
$success	= $pc->POSTisSetOrSetDefault('redirect_on_success', '');
$failure	= $pc->POSTisSetOrSetDefault('redirect_on_failure', '');
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

// Create the query
$query = <<< EOD
SET @aArticleId = {$articleId}; 
CALL PInsertOrUpdateArticle(@aArticleId, '{$userId}', '{$title}', '{$content}');
SELECT @aArticleId AS id;
EOD;

// Perform the query
$res = $db->MultiQuery($query); 

// Use results
$results = Array();
$db->RetrieveAndStoreResultsFromMultiQuery($results);

// Store inserted/updated article id
$row = $results[2]->fetch_object();
$articleId = $row->id;

$results[2]->close(); 
$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Redirect to another page
// Support $redirect to be local uri within site or external site (starting with http://)
//
$pc->RedirectTo(sprintf($success, $articleId));
exit;

?>