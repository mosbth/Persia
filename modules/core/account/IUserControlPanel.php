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
//
// Variables must be defined by pagecontroller:
// $pc
// $userId 
// $password1
// $password2
// $redirectFail
//
// Include from pagecontroller using:
// include(dirname(__FILE__) . '/IAccountChangePasswordProcess.php');
//
// Messages that may be set in session reflecting the outcome of the action:
// changePwdFailed
// changePwdSuccess
//


// -------------------------------------------------------------------------------------------
//
// General settings for this file.
//
$pc->LoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// Prepare the menu-bar
// 
global $gModule;

$editDetails 	= "?m={$gModule}&amp;p=account-settings";
$download 		= "?m={$gModule}&amp;p=account-update";

$htmlMenuBar = <<< EOD
<div class='section'>
	<h1>{$pc->lang['UCP_TITLE']}</h1>
	<p>{$pc->lang['UCP_DESCRIPTION']}</p>
</div> <!-- section -->

<div class='section'>
	<div class='nav-standard nav-button'>
	<ul>
	<li><a href='{$editDetails}'>{$pc->lang['UCP_MENU_ACCOUNT']}</a> 
	<li><a href='{$download}'>{$pc->lang['UCP_MENU_FILEARCHIVE']}</a>
	</ul>
	<div class='clear'>&nbsp;</div>
	</div>
	<hr>
</div> <!-- section -->
EOD;


?>