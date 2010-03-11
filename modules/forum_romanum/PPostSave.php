<?php
// ===========================================================================================
//
// PPostSave.php
//
// Saves a forum post to database
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
$postId		= $pc->POSTisSetOrSetDefault('post_id', 0);
$topicId	= $pc->POSTisSetOrSetDefault('topic_id', 0);
$success	= $pc->POSTisSetOrSetDefault('redirect_on_success', '');
$failure	= $pc->POSTisSetOrSetDefault('redirect_on_failure', '');
$userId		= $_SESSION['idUser'];

// Always check whats coming in...
$pc->IsNumericOrDie($postId, 0);

// Clean up HTML-tags
$tagsAllowed = '<h1><h2><h3><h4><h5><h6><p><a><br><i><em><b><strong><li><ol><ul><a><style>';
$title 		= strip_tags($title, $tagsAllowed);
$content 	= strip_tags($content, $tagsAllowed);


// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database, get the query and execute it.
// Relates to files in directory TP_SQLPATH.
//
$db 	= new CDatabaseController();
$mysqli = $db->Connect();

// Get the SP names
$spPInsertOrUpdatePost = DBSP_PInsertOrUpdatePost;

// Create the query
$query = <<< EOD
SET @aPostId = {$postId};
SET @aTopicId = {$topicId};
CALL {$spPInsertOrUpdatePost}(@aPostId, @aTopicId, '{$userId}', '{$title}', '{$content}');
SELECT @aPostId AS id;
SELECT @aTopicId AS id;
EOD;

// Perform the query
$res = $db->MultiQuery($query); 

// Use results
$results = Array();
$db->RetrieveAndStoreResultsFromMultiQuery($results);

// Store inserted/updated article id
$row = $results[3]->fetch_object();
$postId = $row->id;

$results[3]->close(); 
$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Redirect to another page
//
$pc->RedirectTo(sprintf($success, $topicId, $postId));
exit;

?>