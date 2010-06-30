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
// File: PGroupDetailsProcess.php
//
// Description: Add/delete group and save details about a group.
//
// Known issues:
// -
//
// History: 
// 2010-06-24: Created.
//


// -------------------------------------------------------------------------------------------
//
// Get common controllers, uncomment if not used in current pagecontroller.
//
// $pc, Page Controller helpers. Useful methods to use in most pagecontrollers
// $uc, User Controller. Keeps information/permission on user currently signed in.
// $if, Interception Filter. Useful to check constraints before entering a pagecontroller.
// $db, Database Controller. Manages all database access.
//
$pc = CPageController::GetInstanceAndLoadLanguage(__FILE__);
$uc = CUserController::GetInstance();
$if = CInterceptionFilter::GetInstance();
$db = CDatabaseController::GetInstance();


// -------------------------------------------------------------------------------------------
//
// Perform checks before continuing, what's to be fullfilled to enter this controller?
//
$if->FrontControllerIsVisitedOrDie();
$if->UserIsSignedInOrRedirectToSignIn();
$if->UserIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
// Always check whats coming in...
//
$submitAction	= $pc->POSTorGETisSetOrSetDefault('do-submit');
$redirect			= $pc->POSTisSetOrSetDefault('redirect');
$redirectFail	= $pc->POSTisSetOrSetDefault('redirect-fail');


// -------------------------------------------------------------------------------------------
//
// Depending on the submit-action, do whats to be done. If, else if, else, replaces switch.
// Start by doing some insane checking to avoid misusage, errormessage if not correct.
// 
if(false) {

}


// -------------------------------------------------------------------------------------------
//
// Save details of a group.
// 
else if($submitAction == 'save-group') {

	// Get the input
	$id						= $pc->POSTisSetOrSetDefault('id');
	$name 				= $pc->POSTisSetOrSetDefault('name');
	$description 	= $pc->POSTisSetOrSetDefault('description');

	// Check boundaries for whats coming in
	// is id unisgned int?
	if(!(is_numeric($id) && $id > 0)) {
		$pc->SetSessionMessage('failedDetails', $pc->lang['ID_INVALID']);
		$pc->RedirectTo($redirectFail);
	}

	// trying to change a system defined group?
	if($id <= $db->_['CNrOfSystemGroups']) {
		$pc->SetSessionMessage('failedDetails', $pc->lang['SYSTEM_GROUP']);
		$pc->RedirectTo($redirectFail);
	}

	// is name within size?
	if(mb_strlen($name) > $db->_['CSizeGroupName']) {
		$pc->SetSessionMessage('failedDetails', sprintf($pc->lang['NAME_TO_LONG'], $db->_['CSizeGroupName']));
		$pc->RedirectTo($redirectFail);
	}

	// is description within size?
	if(mb_strlen($description) > $db->_['CSizeGroupDescription']) {
		$pc->SetSessionMessage('failedDetails', sprintf($pc->lang['DESCRIPTION_TO_LONG'], $db->_['CSizeGroupDescription']));
		$pc->RedirectTo($redirectFail);
	}

	// Connect and prepare arguments
	$mysqli = $db->Connect();
	$name 				= $mysqli->real_escape_string($name);
	$description 	= $mysqli->real_escape_string($description);

	// Perform the query and ignore results
	$query 	= <<< EOD
CALL {$db->_['PGroupDetailsUpdate']}({$id}, '{$name}', '{$description}');
EOD;
	$db->DoMultiQueryRetrieveAndStoreResultset($query);
	$mysqli->close();
	
	$pc->SetSessionMessage('successDetails', $pc->lang['ITEM_UPDATED']);
	$pc->RedirectTo($redirect);
}


// -------------------------------------------------------------------------------------------
//
// Add a group through a GET request
// 
else if($submitAction == 'add-group') {

	// What are the actions?
	global $gModule;
	$redirect = empty($redirect) ? "?m={$gModule}&p=acp-groupdetails&id=%d" : $redirect;

	// Get the input
	$name 				= $pc->lang['DEFAULT_NAME'];
	$description 	= $pc->lang['DEFAULT_DESCRIPTION'];

	// Check boundaries for whats coming in
	// is name within size?
	if(mb_strlen($name) > $db->_['CSizeGroupName']) {
		$pc->SetSessionMessage('failedDetails', sprintf($pc->lang['NAME_TO_LONG'], $db->_['CSizeGroupName']));
		$pc->RedirectTo($redirectFail);
	}

	// is description within size?
	if(mb_strlen($description) > $db->_['CSizeGroupDescription']) {
		$pc->SetSessionMessage('failedDetails', sprintf($pc->lang['DESCRIPTION_TO_LONG'], $db->_['CSizeGroupDescription']));
		$pc->RedirectTo($redirectFail);
	}

	// Connect and prepare arguments
	$mysqli = $db->Connect();
	$name 				= $mysqli->real_escape_string($name);
	$description 	= $mysqli->real_escape_string($description);

	// Perform the query and manage results
	$query 	= <<< EOD
CALL {$db->_['PGroupAdd']}(@id, '{$name}', '{$description}');
SELECT @id AS id;
EOD;
	$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);
	
	$row = $results[1]->fetch_object();
	$id = $row->id;
	$results[1]->close();
	$mysqli->close();
	
	$pc->SetSessionMessage('successDetails', $pc->lang['ITEM_ADDED']);
	$pc->RedirectTo(sprintf($redirect, $id));
}


// -------------------------------------------------------------------------------------------
//
// Delete a group through a GET request
// 
else if($submitAction == 'del-group') {

	// Get the input
	$id = $pc->GETisSetOrSetDefault('id');

	// What are the actions?
	global $gModule;
	$redirect 		= empty($redirect) ? "?m={$gModule}&p=acp-groups" : $redirect;
	$redirectFail = empty($redirectFail) ? "?m={$gModule}&p=acp-groupdetails&id={$id}" : $redirect;

	// Check boundaries for whats coming in
	// is id unisgned int?
	if(!(is_numeric($id) && $id > 0)) {
		$pc->SetSessionMessage('failedDetails', $pc->lang['ID_INVALID']);
		$pc->RedirectTo($redirectFail);
	}

	// trying to change a system defined group?
	if($id <= $db->_['CNrOfSystemGroups']) {
		$pc->SetSessionMessage('failedDetails', $pc->lang['SYSTEM_GROUP']);
		$pc->RedirectTo($redirectFail);
	}

	// Connect and perform query, ignore results
	$mysqli = $db->Connect();
	$query 	= <<< EOD
CALL {$db->_['PGroupDelete']}({$id});
EOD;
	$db->DoMultiQueryRetrieveAndStoreResultset($query);
	$mysqli->close();
	
	$pc->SetSessionMessage('successDetails', $pc->lang['ITEM_DELETED']);
	$pc->RedirectTo($redirect);
}


// -------------------------------------------------------------------------------------------
//
// Add groupmembers by GET request
// 
else if($submitAction == 'add-members') {

/*
	// Get the input
	$id						= $pc->POSTisSetOrSetDefault('id');
	$name 				= $pc->POSTisSetOrSetDefault('name');
	$description 	= $pc->POSTisSetOrSetDefault('description');

	// Check boundaries for whats coming in
	// is id unisgned int?
	if(!(is_numeric($id) && $id > 0)) {
		$pc->SetSessionMessage('failedDetails', $pc->lang['ID_INVALID']);
		$pc->RedirectTo($redirectFail);
	}

	// trying to change a system defined group?
	if($id <= $db->_['CNrOfSystemGroups']) {
		$pc->SetSessionMessage('failedDetails', $pc->lang['SYSTEM_GROUP']);
		$pc->RedirectTo($redirectFail);
	}

	// is name within size?
	if(mb_strlen($name) > $db->_['CSizeGroupName']) {
		$pc->SetSessionMessage('failedDetails', sprintf($pc->lang['NAME_TO_LONG'], $db->_['CSizeGroupName']));
		$pc->RedirectTo($redirectFail);
	}

	// is description within size?
	if(mb_strlen($description) > $db->_['CSizeGroupDescription']) {
		$pc->SetSessionMessage('failedDetails', sprintf($pc->lang['DESCRIPTION_TO_LONG'], $db->_['CSizeGroupDescription']));
		$pc->RedirectTo($redirectFail);
	}

	// Connect and prepare arguments
	$mysqli = $db->Connect();
	$name 				= $mysqli->real_escape_string($name);
	$description 	= $mysqli->real_escape_string($description);

	// Perform the query and ignore results
	$query 	= <<< EOD
CALL {$db->_['PGroupDetailsUpdate']}({$id}, '{$name}', '{$description}');
EOD;
	$db->DoMultiQueryRetrieveAndStoreResultset($query);
	$mysqli->close();
	
	$pc->SetSessionMessage('successDetails', $pc->lang['ITEM_UPDATED']);
	$pc->RedirectTo($redirect);
*/

}


// -------------------------------------------------------------------------------------------
//
// Remove a groupmember by GET request
// 
else if($submitAction == 'remove-member') {

	// Get the input
	$gid = $pc->POSTisSetOrSetDefault('gid');
	$mid = $pc->POSTisSetOrSetDefault('mid');

	// Check boundaries for whats coming in
	// is gid unisgned int?
	if(!(is_numeric($id) && $id > 0)) {
		$pc->SetSessionMessage('failedMembers', $pc->lang['ID_INVALID']);
		$pc->RedirectTo($redirectFail);
	}
	
	// is mid unisgned int?
	if(!(is_numeric($id) && $id > 0)) {
		$pc->SetSessionMessage('failedMembers', $pc->lang['MEMBER_ID_INVALID']);
		$pc->RedirectTo($redirectFail);
	}

	// Connect and prepare arguments
	$mysqli = $db->Connect();
	$name 				= $mysqli->real_escape_string($name);
	$description 	= $mysqli->real_escape_string($description);

	// Perform the query and ignore results
	$query 	= <<< EOD
CALL {$db->_['PGroupDetailsUpdate']}({$id}, '{$name}', '{$description}');
EOD;
	$db->DoMultiQueryRetrieveAndStoreResultset($query);
	$mysqli->close();
	
	$pc->SetSessionMessage('successDetails', $pc->lang['ITEM_UPDATED']);
	$pc->RedirectTo($redirect);
*/

}


// -------------------------------------------------------------------------------------------
//
// Default, submit-action not supported, show error and die.
// 
die($pc->lang['SUBMIT_ACTION_NOT_SUPPORTED']);


?>