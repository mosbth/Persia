<?php
// ===========================================================================================
//
// PTopicShow.php
//
// Show the content of a topic.
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
//$intFilter->UserIsSignedInOrRecirectToSignIn();
//$intFilter->UserIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$topicId	= $pc->GETisSetOrSetDefault('id', 0);
$userId		= $_SESSION['idUser'];

// Always check whats coming in...
$pc->IsNumericOrDie($topicId, 0);


// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database, get the query and execute it.
// Relates to files in directory TP_SQLPATH.
//
$db 	= new CDatabaseController();
$mysqli = $db->Connect();

// Get the SP names
$spPGetTopicDetailsAndPosts = DBSP_PGetTopicDetailsAndPosts;

$query = <<< EOD
CALL {$spPGetTopicDetailsAndPosts}({$topicId});
EOD;

// Perform the query
$results = Array();
$res = $db->MultiQuery($query); 
$db->RetrieveAndStoreResultsFromMultiQuery($results);
	
// Get article details
$row = $results[0]->fetch_object();
$title 			= $row->title;
$createdBy		= $row->creator;
$createdWhen	= $row->created;
$lastPostBy 	= $row->lastpostby;
$lastPostWhen	= $row->lastpostwhen;
$numPosts		= $row->postcounter;
$results[0]->close(); 

// Get the list of posts
$list = <<<EOD
<table width='99%'>
<tr>
<th width='60%'>
Topic
</th>
<th>
Posts
</th>
<th colspan='2'>
Most recent
</th>
</tr>
EOD;
while($row = $results[1]->fetch_object()) {    
	$list .= <<<EOD
<tr>
<td>
<a href='?m=rom&amp;p=topic&amp;id={$row->postid}'>{$row->title}</a>
</td>
<td>
{$row->content}
</td>
<td>
{$row->username}
</td>
<td>
{$row->created}
</td>
</tr>
EOD;
}
$list .= "</table>";

$results[1]->close(); 
$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// User is admin or is owner of this topic
//
/*
$ownerMenu = "";
if($intFilter->IsUserMemberOfGroupAdminOrIsCurrentUser($owner)) {
	$ownerMenu = <<<EOD
[
<a href="?m=rom&amp;p=post-edit&amp;editor=markItUp&amp;id={$topicId}">edit</a>
]
EOD;
}
*/


// -------------------------------------------------------------------------------------------
//
// Page specific code
//
/*
<p>{$content}</p>
<p class='notice'>
By {$username}. Updated: {$saved}. {$ownerMenu}
</p>
*/

$htmlMain = <<<EOD
<h1>{$title}</h1>

EOD;

$htmlLeft 	= "";
$htmlRight	= <<<EOD
<h3 class='columnMenu'>About This Topic</h3>
<p>
Created by {$createdBy} {$createdWhen}.<br>
Last reply {$lastPostBy} {$lastPostWhen}
$numPosts posts.<br>
</p>

Later...num viewed, latest accessed. Tags.
</p>
<h3 class='columnMenu'>Related Topics</h3>
<p>
Later...Do search, show equal (and hot/popular) topics
</p>
<h3 class='columnMenu'>About Author</h3>
<p>
Later...
</p>
<h3 class='columnMenu'>More by this author</h3>
<p>
Later...
</p>
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->PrintPage("Topic: {$title}", $htmlLeft, $htmlMain, $htmlRight);
exit;

?>