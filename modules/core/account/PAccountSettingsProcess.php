<?php
// ===========================================================================================
//
// File: PAccountSettingsProcess.php
//
// Description: Save changes in profile and account settings.
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
$submitAction	= $pc->POSTisSetOrSetDefault('submit');
$accountId		= $pc->POSTisSetOrSetDefault('accountid');
$redirect			= $pc->POSTisSetOrSetDefault('redirect');
$redirectFail	= $pc->POSTisSetOrSetDefault('redirect-failure');
$userId				= $_SESSION['idUser'];

// Always check whats coming in...
//$pc->IsNumericOrDie($topicId, 0);


// -------------------------------------------------------------------------------------------
//
// Depending on the submit-action, do whats to be done. If, else if, else, replaces switch.
// 


// -------------------------------------------------------------------------------------------
//
// Do some insane checking to avoid misusage, errormessage if not correct.
// 
// Are we trying to change the same user profile as is signed in? Must be Yes.
//
if($userId != $accountId) {


	echo "Missusage" . $submitAction;
	die;
}


// -------------------------------------------------------------------------------------------
//
// Change the password
// 
else if($submitAction == 'change-password') {

	$password1	= $pc->POSTisSetOrSetDefault('password1');
	$password2	= $pc->POSTisSetOrSetDefault('password2');

	if(empty($password1) || empty($password2)) {
		$pc->SetSessionErrorMessage($pc->lang['PASSWORD_CANNOT_BE_EMPTY']);
		$pc->RedirectTo($redirectFail);
	} 
	else if($password1 != $password2) {
		$pc->SetSessionErrorMessage($pc->lang['PASSWORD_DOESNT_MATCH']);
		$pc->RedirectTo($redirectFail);
	}

	// Execute the database query to make the update
	$db = new CDatabaseController();
	$mysqli = $db->Connect();

	// Get the SP names
	$spChangePassword	= DBSP_PChangeAccountPassword;

	$query  = sprintf("CALL {$spChangePassword}(%s, '%s');", $userId, $mysqli->real_escape_string($password1));
	$query .= "SELECT ROW_COUNT() AS rowsaffected;";
	
	// Perform the query
	$results = Array();
	$res = $db->MultiQuery($query); 
	$db->RetrieveAndStoreResultsFromMultiQuery($results);

	// Get details from resultset
	$row = $results[1]->fetch_object();

	$mysqli->close();

	// Redirect to resultpage
	$pc->RedirectTo($redirect);
}


// -------------------------------------------------------------------------------------------
//
// Change email
// 
else if($submitAction == 'change-email') {

	$email	= $pc->POSTisSetOrSetDefault('email');

	// Execute the database query to make the update
	$db = new CDatabaseController();
	$mysqli = $db->Connect();

	// Get the SP names
	$spChangeEmail	= DBSP_PChangeAccountEmail;

	$query  = sprintf("CALL {$spChangeEmail}(%s, '%s');", $userId, $mysqli->real_escape_string($email));
	$query .= "SELECT ROW_COUNT() AS rowsaffected;";
	
	// Perform the query
	$results = Array();
	$res = $db->MultiQuery($query); 
	$db->RetrieveAndStoreResultsFromMultiQuery($results);

	// Get details from resultset
	$row = $results[1]->fetch_object();

	$mysqli->close();

	// Redirect to resultpage
	$pc->RedirectTo($redirect);
}


// -------------------------------------------------------------------------------------------
//
// Change avatar
// 
else if($submitAction == 'change-avatar') {

	$avatar	= $pc->POSTisSetOrSetDefault('avatar');

	// Execute the database query to make the update
	$db = new CDatabaseController();
	$mysqli = $db->Connect();

	// Get the SP names
	$spChangeAvatar	= DBSP_PChangeAccountAvatar;

	$query  = sprintf("CALL {$spChangeAvatar}(%s, '%s');", $userId, $mysqli->real_escape_string($avatar));
	$query .= "SELECT ROW_COUNT() AS rowsaffected;";
	
	// Perform the query
	$results = Array();
	$res = $db->MultiQuery($query); 
	$db->RetrieveAndStoreResultsFromMultiQuery($results);

	// Get details from resultset
	$row = $results[1]->fetch_object();

	$mysqli->close();

	// Redirect to resultpage
	$pc->RedirectTo($redirect);
}


// -------------------------------------------------------------------------------------------
//
// Default, submit-action not supported, show error and die.
// 
else {

	echo $submitAction;
	die;
}


exit;

// -------------------------------------------------------------------------------------------
//
// Redirect to another page
//
$pc->RedirectTo($pc->POSTisSetOrSetDefault('redirect'));
exit;



// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->PrintPage(sprintf($pc->lang['SETTINGS_FOR'], $account), $htmlLeft, $htmlMain, $htmlRight);
exit;



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

/*
$urlToAddReply = "?m={$gModule}&amp;p=post-edit&amp;topic={$topicId}";

$htmlMain = <<<EOD
<h1>{$title}</h1>
{$posts}
<p>
<a href='{$urlToAddReply}'>Add reply</a>
</p>
EOD;
*/

$htmlMain = <<< EOD
<h1>{$pc->lang['MANAGE_ACCOUNT']}</h1>
<form>

<h2>{$pc->lang['BASIC_ACCOUNT_INFO']}</h2>
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
<button class='' type="submit" name="submit-change-password">{$pc->lang['CHANGE_PASSWORD']}</button>
</td>
</tr>
</table>
</fieldset>

<h2>{$pc->lang['EMAIL_SETTINGS']}</h2>
<fieldset class='accountsettings'>
<table width='99%'>
<tr>
<td>{$pc->lang['EMAIL_LABEL']}</td>
<td style='text-align: right;'><input class='email' type='text' name='email' value='{$email}'></td>
</tr>
<tr>
<td colspan='2' style='text-align: right;'>
<button class='' type="submit" name="submit-change-email">{$pc->lang['UPDATE_EMAIL']}</button>
</td>
</tr>
</table>
</fieldset>


<h2>{$pc->lang['AVATAR_SETTINGS']}</h2>
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
<button class='' type="submit" name="submit-change-avatar">{$pc->lang['UPDATE_AVATAR']}</button>
</td>
</tr>
</table>
</fieldset>


<h2>{$pc->lang['GROUP_SETTINGS']}</h2>
<fieldset class='accountsettings'>
<table width='99%'>
<tr>
<td>{$pc->lang['GROUPMEMBER_OF_LABEL']}</td>
<td style='text-align: right;'><input class='groups' type='text' name='groups' value='{$groupakronym}'></td>
</tr>
<tr>
<td colspan='2' style='text-align: right;'>
<button class='' type="submit" name="submit-change-groups">{$pc->lang['UPDATE_GROUPS']}</button>
</td>
</tr>
</table>
</fieldset>

</form>

EOD;


$htmlLeft 	= "";
$htmlRight	= <<<EOD
<h3 class='columnMenu'>About Privacy</h3>
<p>
Later...
</p>

EOD;



?>