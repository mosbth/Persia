<?php
// ===========================================================================================
//
// PArticleEdit.php
//
// A WYSIWYG editor
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
$title 		= "";
$content 	= "";

// Connect
$db 	= new CDatabaseController();
$mysqli = $db->Connect();

// Get the SP names
$spPGetArticleDetailsAndArticleList	= DBSP_PGetArticleDetailsAndArticleList;

$query = <<< EOD
CALL {$spPGetArticleDetailsAndArticleList}({$articleId}, '{$userId}');
EOD;

// Perform the query
$results = Array();
$res = $db->MultiQuery($query); 
$db->RetrieveAndStoreResultsFromMultiQuery($results);

// Get article details
$row = $results[0]->fetch_object();
$title 		= $row->title;
$content 	= $row->content;
$saved	 	= empty($row->latest) ? 'Not yet' : $row->latest;
$results[0]->close(); 

// Get the list of articles
$list = "";
while($row = $results[1]->fetch_object()) {    
	$list .= "<a title='{$row->info}' href='?p=article-edit&amp;article-id={$row->id}'>{$row->title}</a><br>";
}
$results[1]->close(); 

$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Page specific code
//
$htmlMain = <<<EOD
<form class='editor1' action='?p=article-save' method='POST'>
<input type='hidden' name='redirect_on_success' value='article-edit&amp;article-id=%1\$d'>
<input type='hidden' name='redirect_on_failure' value='article-edit&amp;article-id=%1\$d'>
<input type='hidden' name='article_id' value='{$articleId}'>
<p>
Title: <input class='title' type='text' name='title' value='{$title}'>
</p>
<p>
<textarea class='size500x400' name='content'>{$content}</textarea>
</p>
<p class="notice">
Saved: {$saved}
</p>
<p>
<input type='button' value='Save' onClick='submit();'>
<input type='button' value='Save & Show' onClick='form.redirect_on_success.value="article-show&amp;article-id=%1\$d"; submit();'>
<input type='button' value='Copy to new' onClick='form.article_id.value="0"; submit();'>
<input type='button' value='Delete' onClick='if(confirm("Do you REALLY want to delete it?")) {form.action="?p=article-delete"; submit();}'>
</p>
<p class='small'>
Current editor is: Original
</p>
<p class='small'>
Edit this using 
<a href='?p=article-edit&amp;article-id={$articleId}'>Original</a> | 
<a href='?p=article-nicedit&amp;article-id={$articleId}'>NicEdit</a> |
<a href='?p=article-wymeditor&amp;article-id={$articleId}'>WYMeditor</a> |
<a href='?p=article-markitup&amp;article-id={$articleId}'>markItUp!</a> 
</p>
</form>

EOD;

$htmlLeft 	= "";
$htmlRight	= <<<EOD
<h3 class='columnMenu'>Actions</h3>
<p>
<a href="?p=article-show&amp;article-id={$articleId}">Show this article...</a>
<br>
<a href="?p=article-edit">Create new article...</a>
<br>
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

$page->printPage('Edit article', $htmlLeft, $htmlMain, $htmlRight);
exit;

?>