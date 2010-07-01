<?php
// ===========================================================================================
//
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
// File: IAccountChangePasswordProcess.php
//
// Description: Submit-action to change password. 
// To be included in a pagecontroller that has the correct environment set.
// Its in a own file since several pagecontroller needs this action.
// I considered doing this in a function, class or some other way. But
// I have not found a better solution than to simply include it. 
// This type should actually have a name, for example pagecontrollerinclude.
// Its a way to organise the functions in a webbapplication.
//
// Author: Mikael Roos, mos@bth.se
//
// Preconditions:
//
// Variables must be defined by pagecontroller:
// $userId 
// $password1
// $password2
// $redirectFail
//
// Include from pagecontroller using:
// require(dirname(__FILE__) . '/IAccountChangePasswordProcess.php');
//
// Messages that may be set in session reflecting the outcome of the action:
// changePwdFailed
// changePwdSuccess
//
// Known issues:
// -
//
// History: 
// 2010-07-01: New structure for instantiating controllers. Included license message.
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
$db = CDatabaseController::GetInstance();


// -------------------------------------------------------------------------------------------
//
// Change the password
// 
$password1	= $pc->POSTisSetOrSetDefault('password1');
$password2	= $pc->POSTisSetOrSetDefault('password2');

if(empty($password1) || empty($password2)) {
	$pc->SetSessionMessage('changePwdFailed', $pc->lang['PASSWORD_CANNOT_BE_EMPTY']);
	$pc->RedirectTo($redirectFail);
} 
else if($password1 != $password2) {
	$pc->SetSessionMessage('changePwdFailed', $pc->lang['PASSWORD_DOESNT_MATCH']);
	$pc->RedirectTo($redirectFail);
}

//
// Connect to database and prepare query
// Can use either userid or account to find the user. 
// See implementation of the procedure.
//
$mysqli = $db->Connect();
$password = $mysqli->real_escape_string($password1);
$query = <<<EOD
CALL {$db->_['PChangeAccountPassword']}('{$userId}', '{$password}', @rowcount);
SELECT @rowcount AS rowcount;
EOD;

// Perform the query, 
$results = $db->DoMultiQueryRetrieveAndStoreResultset($query);

$row = $results[1]->fetch_object();

if($row->rowcount == 1) {
	$pc->SetSessionMessage('changePwdSuccess', $pc->lang['CHANGE_PASSWORD_SUCCESS']);
} else {
	$pc->SetSessionMessage('changePwdFailed', $pc->lang['PASSWORD_NOT_UPDATED']);
	$pc->RedirectTo($redirectFail);
}

$results[1]->close();
$mysqli->close();


?>