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
//$topicId	= $pc->GETisSetOrSetDefault('id', 0);
$userId		= $_SESSION['idUser'];

// Always check whats coming in...
//$pc->IsNumericOrDie($topicId, 0);


// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database, get the query and execute it.
// Relates to files in directory TP_SQLPATH.
//
$db 	= new CDatabaseController();
$mysqli = $db->Connect();

// Get the SP names
$spAccountDetails = DBSP_PGetAccountDetails;

$query = <<< EOD
CALL {$spAccountDetails}({$userId});
EOD;

// Perform the query
$results = Array();
$res = $db->MultiQuery($query); 
$db->RetrieveAndStoreResultsFromMultiQuery($results);
	
// Get account details 	
$row = $results[0]->fetch_object();
$account 			= $row->account;
$name					= $row->name;
$email				= $row->email;
$avatar 			= $row->avatar;
$groupakronym	= $row->groupakronym;
$groupdesc		= $row->groupdesc;
$results[0]->close(); 

$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Page specific code
//
global $gModule;

$action 	= "?m={$gModule}&amp;p=account-update";
$redirect = "?m={$gModule}&amp;p=account-settings";

$htmlMain = <<< EOD
<h1>{$pc->lang['MANAGE_ACCOUNT']}</h1>

<h2 id='basic'>{$pc->lang['BASIC_ACCOUNT_INFO']}</h2>
<form action='{$action}' method='POST'>
<input type='hidden' name='redirect' 					value='{$redirect}#basic'>
<input type='hidden' name='redirect-failure' 	value='{$redirect}'>
<input type='hidden' name='accountid' 				value='{$userId}'>
<fieldset class='accountsettings'>
<table width='99%'>
<tr>
<td>{$pc->lang['ACCOUNT_NAME_LABEL']}</td>
<td style='text-align: right;'><input class='account' type='text' name='account' readonly value='{$account}'></td>
</tr>
<tr>
<td>{$pc->lang['ACCOUNT_PASSWORD_LABEL']}</td>
<td style='text-align: right;'><input class='password' type='password' name='password1'></td>
</tr>
<tr>
<td>{$pc->lang['ACCOUNT_PASSWORD_AGAIN_LABEL']}</td>
<td style='text-align: right;'><input class='password' type='password' name='password2'></td>
</tr>
<tr>
<td colspan='2' style='text-align: right;'>
<button type='submit' name='submit' value='change-password'>{$pc->lang['CHANGE_PASSWORD']}</button>
</td>
</tr>
</table>
</fieldset>
</form>

<h2 id='email'>{$pc->lang['EMAIL_SETTINGS']}</h2>
<form action='{$action}' method='POST'>
<input type='hidden' name='redirect' 					value='{$redirect}#email'>
<input type='hidden' name='redirect-failure' 	value='{$redirect}'>
<input type='hidden' name='accountid' 				value='{$userId}'>
<fieldset class='accountsettings'>
<table width='99%'>
<tr>
<td>{$pc->lang['EMAIL_LABEL']}</td>
<td style='text-align: right;'><input class='email' type='text' name='email' value='{$email}'></td>
</tr>
<tr>
<td colspan='2' style='text-align: right;'>
<button type='submit' name='submit' value='change-email'>{$pc->lang['UPDATE_EMAIL']}</button>
</td>
</tr>
</table>
</fieldset>
</form>

<h2 id='avatar'>{$pc->lang['AVATAR_SETTINGS']}</h2>
<form action='{$action}' method='POST'>
<input type='hidden' name='redirect' 					value='{$redirect}#avatar'>
<input type='hidden' name='redirect-failure' 	value='{$redirect}'>
<input type='hidden' name='accountid' 				value='{$userId}'>
<fieldset class='accountsettings'>
<table width='99%'>
<tr>
<td>{$pc->lang['AVATAR_LABEL']}</td>
<td style='text-align: right;'><input class='avatar' type='text' name='avatar' value='{$avatar}'></td>
</tr>
<tr>
<td>
<img src='{$row->avatar}'>
</td>
<td style='text-align: right;'>
<button type='submit' name='submit' value='change-avatar'>{$pc->lang['UPDATE_AVATAR']}</button>
</td>
</tr>
</table>
</fieldset>
</form>

<!--
<h2>{$pc->lang['GROUP_SETTINGS']}</h2>
<fieldset class='accountsettings'>
<table width='99%'>
<tr>
<td>{$pc->lang['GROUPMEMBER_OF_LABEL']}</td>
<td style='text-align: right;'><input class='groups' type='text' name='groups' value='{$groupakronym}'></td>
</tr>
<tr>
<td colspan='2' style='text-align: right;'>
<button type='submit' name='submit' value='change-groups'>{$pc->lang['UPDATE_GROUPS']}</button>
</td>
</tr>
</table>
</fieldset>
-->


EOD;


$htmlLeft 	= "";
$htmlRight	= <<<EOD
<h3 class='columnMenu'>About Privacy</h3>
<p>
Later...
</p>

EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->PrintPage(sprintf($pc->lang['SETTINGS_FOR'], $account), $htmlLeft, $htmlMain, $htmlRight);
exit;

?>