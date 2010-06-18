<?php
// ===========================================================================================
//
// File: IUserControlPanel.php
//
// Description: Create a title, description and menu-bar where all items can be reached.
// The UCP (User Control Panel) is the place for the user to review and modify all settings
// related to the account and the available applications.
//
// Author: Mikael Roos, mos@bth.se
//
// Preconditions:
// -
//
// Variables must be defined by pagecontroller:
// $htmlCp (OUT) contains HTML for the CP.
//
// Require from pagecontroller using:
// require(dirname(__FILE__) . '/IUserControlPanel.php');
//
// Messages that may be set in session reflecting the outcome of the action:
// -
//


// -------------------------------------------------------------------------------------------
//
// General settings for this file.
//
CPageController::GetInstanceAndLoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// Prepare the tab-menu
// 
global $gModule, $gPage;


// An array with all items helps with navigation and menus
$cp = Array(
	'title' => $pc->lang['UCP_TITLE'],
	'description' => $pc->lang['UCP_DESCRIPTION'],
	'link' => "?m={$gModule}&amp;p=",
	'page' => 'ucp',
	'path' => Array(
		'ucp' => $pc->lang['UCP_PATH_LINK'],
	),
	'items-level1' => Array(
		'ucp-account-settings' => $pc->lang['UCP_MENU_ACCOUNT'],
		'ucp-filearchive' => $pc->lang['UCP_MENU_FILEARCHIVE'],
		'ucp-fileupload' => $pc->lang['UCP_MENU_FILEUPLOAD'],	
	),
	'items-level2' => Array(
		'ucp-filearchive' => Array(
			'ucp-filearchive' => $pc->lang['UCP_MENU_FILEARCHIVE'],
			'ucp-filedetails' => $pc->lang['UCP_MENU_FILEDETAILS'],
			//'ucp-fileupload' => $pc->lang['UCP_MENU_FILEUPLOAD'],	
		),
	),
);


// Walkthrough level2 items if defined
$htmlItems2 = '';
$choosenLevel1 = '';
$navPathIsUpdated = false;
foreach($cp['items-level2'] as $key1 => $val1) {

	foreach($val1 as $key2 => $val2) {

		// Is this the current choice?
		$isCurrent = '';
		if($gPage == $key2) {
			$choosenLevel1 = "$key1";	
			$isCurrent = " class='current'";
			
			// Hit, update navigation path level 1...
			$navPathIsUpdated = true;
			$cp['path'][$key1] = $cp['items-level1'][$key1];
			/*
			// and level 1 
			if($key1 != $key2) {
				$cp['path'][$key2] = $cp['items-level2'][$key1][$key2]; 
			}
			*/
		}
		// Should be visible or unvisible
		//$htmlItems2 .= "<li{$isCurrent}><a href='{$cp['link']}{$key2}'>{$val2}</a>";
	}

	// Only display submenu if it has current item
	if(empty($choosenLevel1)) {
		$htmlItems2 = '';
	} 
}


// Walkthough level1 items
$htmlItems1 = "";
foreach($cp['items-level1'] as $key1 => $val1) {

	// Is this the current choice?
	$isCurrent = '';
	if($gPage == $key1 || $choosenLevel1 == $key1) {
		$isCurrent = " class='current'";	

		// Hit, update navigation path level 1 if not already done
		if(!$navPathIsUpdated) {
			$cp['path'][$key1] = $cp['items-level1'][$key1];
		}
	}
	$htmlItems1 .= "<li{$isCurrent}><a href='{$cp['link']}{$key1}'>{$val1}</a>";
}


// Generate item list and update navigationpath with current
$htmlPath = "";
foreach($cp['path'] as $key => $val) {
	$htmlPath .= "<li><a href='{$cp['link']}{$key}'>{$val}</a> &gt;";
}
$htmlPath = substr($htmlPath, 0, -5);


// Create HTML for the control panel
$htmlCp = <<< EOD
<div class='section'>
	<ul class='nav-standard nav-path'>
		{$htmlPath}
	</ul>
	<h1>{$cp['title']}</h1>
	<p>{$cp['description']}</p>
</div> <!-- section -->
 
<div class='section'>
	<ul class='nav-standard nav-menu-tab'>
		{$htmlItems1}
	</ul>
	<ul class='nav-standard'>
		{$htmlItems2}
	</ul>
</div> <!-- section -->
EOD;


?>