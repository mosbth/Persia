<?php
// ===========================================================================================
//
// File: PPostEdit.php
//
// Description: A post editor. Create or edit a post.
//
// Author: Mikael Roos, mos@bth.se
//


// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
$pc = new CPageController();
$pc->LoadLanguage(__FILE__);


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
global $gModule;

$editor		= $pc->GETisSetOrSetDefault('editor', 'WYMeditor');
$postId		= $pc->GETisSetOrSetDefault('id', 0);
$topicId	= $pc->GETisSetOrSetDefault('topic', 0);
$userId		= $pc->SESSIONisSetOrSetDefault('idUser', 0);

// Always check whats coming in...
$pc->IsNumericOrDie($postId, 0);
$pc->IsNumericOrDie($topicId, 0);
$pc->IsStringOrDie($editor);


// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database, get the query and execute it.
// Relates to files in directory TP_SQLPATH.
//

// Connect
$db 	= new CDatabaseController();
$mysqli = $db->Connect();

// Get the SP names
$spPGetTopicDetails = DBSP_PGetTopicDetails;
$spPGetPostDetails	= DBSP_PGetPostDetails;

$query = <<< EOD
CALL {$spPGetTopicDetails}({$topicId}, {$postId});
CALL {$spPGetPostDetails}({$postId});
EOD;

// Perform the query
$results = Array();
$res = $db->MultiQuery($query); 
$db->RetrieveAndStoreResultsFromMultiQuery($results);

// Get topic details
$row = $results[0]->fetch_object();
$topicId 		= empty($row->topicid)	? $topicId : $row->topicid;
$topicTitle	= empty($row->title) 		? '' : $row->title;
$topPost		= empty($row->toppost) 	? 0 : $row->toppost;
$results[0]->close(); 

// Get post details
$row = $results[2]->fetch_object();
$title 			= empty($row->title) 			? $pc->lang['NEW_TITLE'] : $row->title;
$content 		= empty($row->content) 		? '' : $row->content;
$saved	 		= empty($row->latest) 		? $pc->lang['NOT_YET'] : $row->latest;
$results[2]->close(); 

$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Use a JavaScript editor
//
$jsEditor				 	= CWYSIWYGEditorFactory::CreateObject($editor);
$jsEditorTextarea = $jsEditor->GetTextareaSettings();
$jsEditorSubmit 	= $jsEditor->GetSubmitSettings();
$htmlHead					= $jsEditor->GetHTMLHead();
$needjQuery				= $jsEditor->DependsOnjQuery();


// -------------------------------------------------------------------------------------------
//
// Add JavaScript and html head stuff related to JavaScript
//
$js = WS_JAVASCRIPT;
$needjQuery = TRUE;
$htmlHead .= <<<EOD
<!-- jGrowl latest -->
<link rel='stylesheet' href='{$js}/jGrowl/jquery.jgrowl.css' type='text/css' />
<script type='text/javascript' src='{$js}/jGrowl/jquery.jgrowl.js'></script>  

<!-- jQuery Form Plugin, included with jquery.autosave -->
<script type='text/javascript' src='{$js}/jquery.autosave/jquery.form.js'></script>  

<!-- jquery.autosave latest -->
<!-- <script type='text/javascript' src='{$js}/jquery.autosave/jquery.autosave.js'></script>  -->

EOD;


//
// Should response be redirect or json?
//
//$redirectOnSuccess = "?m={$gModule}&p=post-edit&id=%2\$d&editor={$editor}";
$redirectOnSuccess = 'json';
$javaScript = <<<EOD

// ----------------------------------------------------------------------------------------------
//
//
//
$(document).ready(function() {

	// Just showing off jGrowl to see that it works
	$.jGrowl("Hello World. This is Growl. Page is now loaded, or re-loaded, I'm not sure on which...");

	// ----------------------------------------------------------------------------------------------
	//
	// Upgrade form to make Ajax submit
	//
	$('#form1').ajaxForm({
		// return a datatype of json
		dataType: 'json',
		// do stuff before submitting form
		beforeSubmit: function(data, status) {
						$.jGrowl('Saving...');
				},	
		// define a callback function
		success: function(data, status) {
						$.jGrowl('Saved: ' + status + ' at ' + data.timestamp);
						$.jGrowl('Topic: ' + data.topicId + ', post: ' + data.postId);
						$('#topic_id').val(data.topicId);
						$('#post_id').val(data.postId);
				}	
	});


	// ----------------------------------------------------------------------------------------------
	//
	// Event handler for buttons in form. Instead of messing up the html-code with javascript.
	// Using Event bubbling as described in this document:
	// http://docs.jquery.com/Tutorials:AJAX_and_Events
	//
	$('#form1').click(function(event) {

		if ($(event.target).is('button#publish')) {
			;
		} else if ($(event.target).is('button#savenow')) {
			;			
    } else if ($(event.target).is('button#discard')) {
			history.back();
    } else if ($(event.target).is('a#viewPost')) {
			$.jGrowl('View post?');
			$('a#viewPost').attr('href', '?m={$gModule}&p=topic&id=' + $('#topic_id').val() + '#post-' + $('#post_id').val());
		}
	});
});

EOD;


// -------------------------------------------------------------------------------------------
//
// Page specific code
//

// Change form depending on usage
$h1 				= '';
$titleForm 	= '';

if($topicId == 0 && $postId == 0) {
	$h1 				= $pc->lang['CREATE_NEW_TOPIC'];
	$titleForm 	= "{$pc->lang['TOPIC']}: <input class='title' type='text' name='title' value='{$title}'>";
} else if($topicId != 0 && $postId == 0) {
	$h1 				= $pc->lang['ADD_REPLY'];
	$titleForm 	= "<h2>{$pc->lang['IN_TOPIC']}: \"{$topicTitle}\"</h2>";
} else if($postId != 0 && $postId == $topPost) {
	$h1					= $pc->lang['EDIT_POST'];
	$titleForm 	= "{$pc->lang['TOPIC']}: <input class='title' type='text' name='title' value='{$title}'>";
} else if($postId != 0) {
	$h1					= $pc->lang['EDIT_POST'];
	$titleForm 	= "<h2>{$pc->lang['IN_TOPIC']}: \"{$topicTitle}\"</h2>";
}

// Only show title if new topic
$formTitle = "";
$formTitle = ($topicId == 0) ? $formTitle : '';
$img = WS_IMAGES;

$htmlMain = <<<EOD
<h1>{$h1}</h1>
<fieldset class='article'>
<form id='form1' action='?m={$gModule}&amp;p=post-save' method='POST'>
<input type='hidden' id='redirect_on_success' name='redirect_on_success' value='{$redirectOnSuccess}'>
<input type='hidden' id='redirect_on_failure' name='redirect_on_failure' value=''>
<input type='hidden' id='post_id' name='post_id' value='{$postId}'>
<input type='hidden' id='topic_id' name='topic_id' value='{$topicId}'>
<p>
{$titleForm}
</p>
<p>
<textarea {$jsEditorTextarea} name='content'>{$content}</textarea>
</p>
<p>
<button id='publish' type='submit' {$jsEditorSubmit}><img src='{$img}/silk/accept.png' alt=''> {$pc->lang['PUBLISH']}</button>
<button id='savenow' type='submit' {$jsEditorSubmit}><img src='{$img}/silk/disk.png' alt=''> {$pc->lang['SAVE_NOW']}</button>
<button id='discard' type='reset'><img src='{$img}/silk/cancel.png' alt=''> {$pc->lang['DISCARD']}</button>
</p>
<p>
<a id='viewPost' title='Click to view the published post' href='?m={$gModule}&amp;p=topic&amp;id={$topicId}#post-{$postId}'>View post</a>
</p>
<!--
<input type='button' value='Delete' onClick='if(confirm("Do you REALLY want to delete it?")) {form.action="?p=article-delete"; form.redirect_on_success.value="?m=rom&amp;p=topics"; submit();}'>
-->
<!--
<p class='notice'>
Saved: {$saved}
</p>
-->
</form>

EOD;

$htmlLeft 	= "";
$htmlRight	= <<<EOD
<h3 class='columnMenu'>{$pc->lang['CHANGE_EDITOR']}</h3>
<p>
<a href='?m={$gModule}&amp;p=post-edit&amp;editor=plain&amp;id={$postId}&amp;topic={$topicId}'>Plain</a> | 
<a href='?m={$gModule}&amp;p=post-edit&amp;editor=NicEdit&amp;id={$postId}&amp;topic={$topicId}'>NicEdit</a> |
<a href='?m={$gModule}&amp;p=post-edit&amp;editor=WYMeditor&amp;id={$postId}&amp;topic={$topicId}'>WYMeditor</a> |
<a href='?m={$gModule}&amp;p=post-edit&amp;editor=markItUp&amp;id={$postId}&amp;topic={$topicId}'>markItUp!</a> 
</p>
<!--
<h3 class='columnMenu'>About This Topic</h3>
<p>
Later...Created by, num posts, num viewed, latest accessed. Tags.
</p>
<h3 class='columnMenu'>Related Topics</h3>
<p>
Later...Do search, show equal (and hot/popular) topics
</p>
-->
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->PrintPage($pc->lang['CREATE_OR_EDIT_POST'], $htmlLeft, $htmlMain, $htmlRight, $htmlHead, $javaScript, $needjQuery);
exit;

?>