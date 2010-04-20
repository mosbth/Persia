<?php
// ===========================================================================================
//
// File: PAccountSettings.php
//
// Description: Show the users profile information in a form and make it possible to edit 
// the information.
//
// Author: Mikael Roos, mos@bth.se
//


<?php
// ===========================================================================================
//
// PTopicShow.php
//
// Show the content of a topic, including topic details and all posts.
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
//$userId		= $_SESSION['idUser'];

// Always check whats coming in...
$pc->IsNumericOrDie($topicId, 0);


// -------------------------------------------------------------------------------------------
//
// User is admin or owner of this post
//
global $gModule;
$imageLink = WS_IMAGES;

$urlToEditPost = "?m={$gModule}&amp;p=post-edit&amp;id=";

$postEditMenu = <<<EOD

EOD;

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
	
// Get topic details
$row = $results[0]->fetch_object();
$title 				= $row->title;
$createdBy		= $row->creator;
$createdWhen	= $row->created;
$lastPostBy 	= $row->lastpostby;
$lastPostWhen	= $row->lastpostwhen;
$numPosts			= $row->postcounter;
$results[0]->close(); 

// Get the list of posts
$posts = <<<EOD
<table width='99%'>
EOD;
while($row = $results[1]->fetch_object()) {

	$isEditable = "<a title='Edit this post' href='{$urlToEditPost}{$row->postid}'><img style='border-style: none;' src='{$imageLink}/edit_14x14.png'></a>";
	$isEditable = ($intFilter->IsUserMemberOfGroupAdminOrIsCurrentUser($row->userid)) ? $isEditable : '';
	
	$posts .= <<<EOD
<tr>
<td width='20%'style='border-bottom: solid 2px #eee'>
<img src='{$row->avatar}'><br>
<p class='small'>
{$row->username}<br>
{$row->created}
</p>
</td>
<td style='border-bottom: solid 2px #eee; text-align: left; vertical-align: top;'>
<div style='float: right;'>
{$isEditable}
<a class='noUnderline' name='post-{$row->postid}' title='Link to this post' href='#post-{$row->postid}'>#</a>
</div>
{$row->content}
</td>
</tr>
EOD;
}
$posts .= "</table>";

$results[1]->close(); 
$mysqli->close();


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
global $gModule;

$urlToAddReply = "?m={$gModule}&amp;p=post-edit&amp;topic={$topicId}";

$htmlMain = <<<EOD
<h1>{$title}</h1>
{$posts}
<p>
<a href='{$urlToAddReply}'>Add reply</a>
</p>
EOD;

$htmlLeft 	= "";
$htmlRight	= <<<EOD
<h3 class='columnMenu'>About This Topic</h3>
<p>
Created by {$createdBy} {$createdWhen}.<br>
</p>
<p>
$numPosts posts.<br>
</p>
<p>
Last reply by {$lastPostBy} {$lastPostWhen}<br>
</p>

<!--
Later...<br>
(num viewed, latest accessed. Tags. Solved. Posted in Category.)
</p>
<h3 class='columnMenu'>Related Topics</h3>
<p>
Later...<br>
(Do search, show equal (and hot/popular) topics)
</p>
<h3 class='columnMenu'>About Author</h3>
<p>
Later...
</p>
<h3 class='columnMenu'>More by this author</h3>
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

$page->PrintPage("Topic: {$title}", $htmlLeft, $htmlMain, $htmlRight);
exit;

?>






// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
require_once(TP_SOURCEPATH . 'CPagecontroller.php');

$pc = new CPagecontroller();
//$pc->LoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// Interception Filter, access, authorithy and other checks.
//
require_once(TP_SOURCEPATH . 'CInterceptionFilter.php');

$intFilter = new CInterceptionFilter();

$intFilter->frontcontrollerIsVisitedOrDie();
$intFilter->userIsSignedInOrRecirectToSign_in();
//$intFilter->userIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of global pageController settings, can exist for several pagecontrollers.
// Decide how page is displayed, review CHTMLPage for supported types.
//
$displayAs = $pc->GETisSetOrSetDefault('pc_display', '');


// -------------------------------------------------------------------------------------------
//
// Page specific code
//

$settingsMenu = $pc->GetSidebarMenu(unserialize(MENU_SETTINGSBAR));

$htmlLeft = <<<EOD
<div class='sidebox'>
<fieldset>
<div id='settingsbar'>
<h4>Inställningar</h4>
{$settingsMenu}
</div>
</fieldset>
</div>
EOD;

$headerMenu = $pc->GetSidebarMenu(unserialize(MENU_ACCOUNTBAR));

$htmlMain = <<<EOD
<h1>Konto</h1>
<div id='settingsmenu'>
{$headerMenu}
</div>
EOD;

$htmlRight = "";


// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database.
//
$mysqli = $pc->ConnectToDatabase();


// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//
$query = "";

$user = $_SESSION['accountUser'];

require_once(TP_SQLPATH . "SUserDetails.php");

$res = $pc->Query($query);


// -------------------------------------------------------------------------------------------
//
// Show the results of the query
//
$row = $res->fetch_object();

$htmlMain .= <<< EOD
<fieldset>
<table border='0'>
<tr>
<th>Id</th>
<td><input type='text' tab='10' name='idUser' size='80' readonly value='{$row->idUser}'></td>
</tr>
<tr>
<th>Account</th>
<td><input type='text' tab='11' name='accountUser' readonly size='80' value='{$row->accountUser}'></td>
</tr>
<tr>
<th>Email</th>
<td><input type='text' tab='12' name='emailUser' readonly size='80' value='{$row->emailUser}'></td>
</tr>
<tr>
<th>Group</th>
<td><input type='text' tab='13' name='idGroup' readonly size='80' value='{$row->idGroup}'></td>
</tr>
<tr>
<th>Group description</th>
<td><input type='text' tab='13' name='nameGroup' readonly size='80' value='{$row->nameGroup}'></td>
</tr>
</table>
</fieldset>
EOD;


// -------------------------------------------------------------------------------------------
//
// Use the results of the query 
//

$res->close();


// -------------------------------------------------------------------------------------------
//
// Close the connection to the database
//
$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
require_once(TP_SOURCEPATH . 'CHTMLPage.php');

$page = new CHTMLPage(WS_STYLESHEET);

$page->printPage($htmlLeft, $htmlMain, $htmlRight, 'Inställningar Konto', $displayAs);
exit;

?>