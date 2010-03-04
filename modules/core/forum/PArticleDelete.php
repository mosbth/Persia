<?php
// ===========================================================================================
//
// PArticleDelete.php
//
// Deletes an article from the database
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

// Table names
$tArticle = DBT_Article;

// Create the query
$query = <<< EOD
UPDATE {$tArticle}
SET 
	deletedArticle = NOW()
WHERE
	idArticle = {$articleId} AND
	Article_idUser = {$userId} AND
	deletedArticle IS NULL
LIMIT 1;
EOD;

// Perform the query
$res = $db->Query($query); 
$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Redirect to another page
// Support $redirect to be local uri within site or external site (starting with http://)
//
$pc->RedirectTo(sprintf($success, 0));
exit;

?>