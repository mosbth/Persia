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
// Prepare the tab-menu
// 
global $gModule, $gPage;

$link = "?m={$gModule}&amp;p=";

$items = Array(
	'account-settings' => $pc->lang['UCP_MENU_ACCOUNT'],
	'ucp-filearchive' => $pc->lang['UCP_MENU_FILEARCHIVE'],
);

$htmlItems = "";
foreach($items as $key => $val) {
	$current = ($gPage == $key) ? " class='current'" : "";
	$htmlItems .= "<li{$current}><a href='{$link}{$key}'>{$val}</a>";
}

$htmlMenuBar = <<< EOD
<div class='section'>
	<h1>{$pc->lang['UCP_TITLE']}</h1>
	<p>{$pc->lang['UCP_DESCRIPTION']}</p>
</div> <!-- section -->

<div class='section'>
	<ul class='nav-standard nav-menu-tab'>
		{$htmlItems}
	</ul>
</div> <!-- section -->

EOD;


?>