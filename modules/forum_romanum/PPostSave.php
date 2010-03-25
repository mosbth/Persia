<?php
// ===========================================================================================
//
// File: PPostSave.php
//
// Description: Saves a forum post to database
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
$action		= $pc->POSTisSetOrSetDefault('action', '');
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
$spPInsertOrUpdatePost 	= DBSP_PInsertOrUpdatePost;
$spPGetPostDetails			= DBSP_PGetPostDetails;
$tArticle 		= DBT_Article;

// Create the query
$query = <<< EOD
SET @aPostId = {$postId};
SET @aTopicId = {$topicId};
CALL {$spPInsertOrUpdatePost}(@aPostId, @aTopicId, @isPublished, @hasDraft, '{$userId}', '{$title}', '{$content}', '{$action}');
SELECT 
	@aPostId AS postId,
	@aTopicId AS topicId,
	@isPublished AS isPublished,
	@hasDraft AS hasDraft,
	NOW() AS timestamp
;

-- For debugging purpose
	SELECT
		A.idArticle AS postid,
		A.titleArticle AS title,
		A.contentArticle AS content,
		A.createdArticle AS created,
		A.modifiedArticle AS modified,
		A.deletedArticle AS deleted,
		A.publishedArticle AS published,
		IF(publishedArticle IS NULL, 0, 1) AS isPublished,
		IF(draftModifiedArticle IS NULL, 0, 1) AS hasDraft,
		A.draftTitleArticle AS draftTitle,
		A.draftContentArticle AS draftContent,
		A.draftModifiedArticle AS draftModified
	FROM {$tArticle} AS A
	WHERE 
		A.idArticle = @aPostId 
	;

EOD;

// Perform the query
$res = $db->MultiQuery($query); 

// Use results
$results = Array();
$db->RetrieveAndStoreResultsFromMultiQuery($results);

// Get inserted/updated id
$row = $results[3]->fetch_object();
$postId 			= $row->postId;
$topicId 			= $row->topicId;
$isPublished 	= (empty($row->isPublished)) ? 0 : 1;
$hasDraft 		= (empty($row->hasDraft)) ? 0 : 1;
$timestamp		= $row->timestamp;
$results[3]->close();

// Get debug info
$row = $results[4]->fetch_object();
$charsTitle 	= strlen($row->title);
$charsContent = strlen($row->content);
$charsDraftTitle 		= strlen($row->draftTitle);
$charsDraftContent 	= strlen($row->draftContent);

$status = "Title:  {$charsTitle} chars<br>Content:  {$charsContent} chars<br>Created: {$row->created}<br>Modified: {$row->modified}<br>Published: {$row->published}<br>Deleted: {$row->deleted}<br>isPublished: {$row->isPublished}<br><br>hasDraft: {$row->hasDraft}<br>DraftTitle:  {$charsDraftTitle} chars<br>DraftContent:  {$charsDraftContent} chars<br>DraftModified: {$row->draftModified}<br>";
$results[4]->close();

$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Redirect to another page
//
if($success = 'json') {
	$json = <<<EOD
{
	"topicId": {$topicId},
	"postId": {$postId},
	"timestamp": "{$timestamp}",
	"isPublished": {$isPublished},
	"hasDraft": {$hasDraft},
	"status": "{$status}"
}
EOD;

	echo $json;

} else {
	$pc->RedirectTo(sprintf($success, $topicId, $postId));
}
exit;

?>