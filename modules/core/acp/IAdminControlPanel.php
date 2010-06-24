<?php
// ===========================================================================================
//
//		Persia (http://phpersia.org), software to build webbapplications.
//    Copyright (C) 2010  Mikael Roos (mos@bth.se)
//
//    This file is part of Persia.
//
//    Persia is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    Persia is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with Persia. If not, see <http://www.gnu.org/licenses/>.
//
// File: IAdminControlPanel.php
//
// Description: Create a title, description and menu-bar where all items can be reached.
// The ACP (Admin Control Panel) is the place for the administrators user to review and 
// modify all settings related to the site and the available applications.
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
// require(dirname(__FILE__) . '/IAdminControlPanel.php');
//
// Messages that may be set in session reflecting the outcome of the action:
// -
//
// Known issues:
// -
//
// History: 
// 2010-06-24: Created.
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
	'title' => $pc->lang['ACP_TITLE'],
	'description' => $pc->lang['ACP_DESCRIPTION'],
	'link' => "?m={$gModule}&amp;p=",
	'page' => 'acp',
	'path' => Array(
		'acp' => $pc->lang['ACP_PATH_LINK'],
	),
	'items-level1' => Array(
		'acp-accounts' => $pc->lang['ACP_MENU_ACCOUNTS'],
		'acp-groups' => $pc->lang['ACP_MENU_GROUPS'],
	),
	'items-level2' => Array(
		'acp-groups' => Array(
			'acp-groups' => $pc->lang['ACP_MENU_GROUPS'],
			'acp-groupdetails' => $pc->lang['ACP_MENU_GROUPDETAILS'],
		),
	),
);

// Is file upload enabled?
/*
if(defined('FILE_ARCHIVE_PATH')) {
	$cp['items-level1']['ucp-filearchive'] = $pc->lang['ACP_MENU_FILEARCHIVE'];
	$cp['items-level1']['ucp-fileupload']  = $pc->lang['ACP_MENU_FILEUPLOAD'];	
}
*/

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