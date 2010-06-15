<?php
// ===========================================================================================
//
// File: SQLCoreCreateDefaultData.php
//
// Description: SQL statements to insert default data for this module.
//
// Author: Mikael Roos, mos@bth.se
//

// Get (or create) an instance of the database object.
$db = CDatabaseController::GetInstance();

// Get pagecontroller helpers to enable language support.
$pc = new CPageController();
$pc->LoadLanguage(__FILE__);

// Create the query
$query = <<<EOD
-- =============================================================================================
--
-- Insert default SQL data, this is needed to get up and running. 
--
-- =============================================================================================

-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Add default groups
--
CALL {$db->_['PCreateGroup']}('admin', '{$pc->lang['SITE_ADMINISTRATORS']}', @aStatus);
CALL {$db->_['PCreateGroup']}('user', '{$pc->lang['LOCAL_USERS']}', @aStatus);

EOD;


$hashingalgoritm = DB_PASSWORDHASHING;
$account 	= 'mikael';
$password	= 'hemligt';
$mail			= "mos@bth.se";
$avatar 	= WS_IMAGES . "man_60x60.png";

$query .= <<<EOD
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Add default user(s) 
--
CALL {$db->_['PCreateAccount']}(@aUserId, '{$account}', '{$password}', '{$hashingalgoritm}', @aStatus);
CALL {$db->_['PChangeAccountEmail']}(@aUserId, '{$mail}', @ignore);
CALL {$db->_['PChangeAccountAvatar']}(@aUserId, '{$avatar}');

EOD;


$account 	= 'doe';
$password	= 'doe';
$mail			= "doe@bth.se";
$avatar 	= WS_IMAGES . "woman_60x60.png";

$query .= <<<EOD
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Add default user(s) 
--
CALL {$db->_['PCreateAccount']}(@aUserId, '{$account}', '{$password}', '{$hashingalgoritm}', @aStatus);
CALL {$db->_['PChangeAccountEmail']}(@aUserId, '{$mail}', @ignore);
CALL {$db->_['PChangeAccountAvatar']}(@aUserId, '{$avatar}');


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Add first user as member of admin group.
--
CALL {$db->_['PGroupMemberAdd']}(1, 1);


EOD;


?>