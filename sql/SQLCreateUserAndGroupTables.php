<?php
// ===========================================================================================
//
// SQLCreateUserAndGroupTables.php
//
// SQL statements to create the tables for the User and group tables.
//
// WARNING: Do not forget to check input variables for SQL injections. 
//
// Author: Mikael Roos
//

// Get (or create) an instance of the database object.
$db = CDatabaseController::GetInstance();

// Get the tablenames
$tGroup 			= DBT_Group;
$tGroupMember = DBT_GroupMember;
$tStatistics 	= DBT_Statistics;

// Get the SP/UDF/trigger names
$trInsertUser			= DBTR_TInsertUser;

$imageLink = WS_IMAGES;

// Create the query
$query = <<<EOD
-- =============================================================================================
--
-- SQL for User, Group and Groupmember
--
-- =============================================================================================

-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Drop all tables first
--
DROP TABLE IF EXISTS {$tGroupMember};
DROP TABLE IF EXISTS {$db->_['User']};
DROP TABLE IF EXISTS {$tGroup};


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Table for the User
--
CREATE TABLE {$db->_['User']} (

  -- Primary key(s)
  idUser INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,

  -- Attributes
  accountUser CHAR(32) NOT NULL UNIQUE,
  nameUser CHAR(100) NULL,
  emailUser CHAR(100) NULL UNIQUE,
  
  -- Attributes related to the password
  saltUser BINARY(10) NOT NULL,
  passwordUser BINARY(40) NOT NULL,
  methodUser CHAR(5) NOT NULL,

	-- Attributes for user profile info
  avatarUser VARCHAR(255) NULL,
  gravatarUser VARCHAR(100) NULL
);


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Table for the Group
--
CREATE TABLE {$tGroup} (

  -- Primary key(s)
  idGroup CHAR(3) NOT NULL PRIMARY KEY,

  -- Attributes
  nameGroup CHAR(40) NOT NULL
);


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Table for the GroupMember
--
CREATE TABLE {$tGroupMember} (

  -- Primary key(s)
  --
  -- The PK is the combination of the two foreign keys, see below.
  --
  
  -- Foreign keys
  GroupMember_idUser INT UNSIGNED NOT NULL,
  GroupMember_idGroup CHAR(3) NOT NULL,
	
  FOREIGN KEY (GroupMember_idUser) REFERENCES {$db->_['User']}(idUser),
  FOREIGN KEY (GroupMember_idGroup) REFERENCES {$tGroup}(idGroup),

  PRIMARY KEY (GroupMember_idUser, GroupMember_idGroup)
  
  -- Attributes

);


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Table for the Statistics
--
DROP TABLE IF EXISTS {$tStatistics};
CREATE TABLE {$tStatistics} (

  -- Primary key(s)
  -- Foreign keys
  Statistics_idUser INT UNSIGNED NOT NULL,
	
  FOREIGN KEY (Statistics_idUser) REFERENCES {$db->_['User']}(idUser),
  PRIMARY KEY (Statistics_idUser),
  
  -- Attributes
  numOfArticlesStatistics INT NOT NULL DEFAULT 0
);


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Create trigger for Statistics
-- Add row when new user is created
--
DROP TRIGGER IF EXISTS {$trInsertUser};
CREATE TRIGGER {$trInsertUser}
AFTER INSERT ON {$db->_['User']}
FOR EACH ROW
BEGIN
  INSERT INTO {$tStatistics} (Statistics_idUser) VALUES (NEW.idUser);
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Function to create a link to gravatar.com from an emailadress.
--
-- http://en.gravatar.com/site/implement/url
--
DROP FUNCTION IF EXISTS {$db->_['FGetGravatarLinkFromEmail']};
CREATE FUNCTION {$db->_['FGetGravatarLinkFromEmail']}
(
	aEmail CHAR(100),
	aSize INT
)
RETURNS CHAR(255)
BEGIN
	DECLARE link CHAR(255);

	-- Take care of empty emailadresses
	IF aEmail IS NULL OR ASCII(aEmail) = 0 THEN
		SET link = '';
	ELSE
		SELECT CONCAT('http://www.gravatar.com/avatar/', MD5(LOWER(aEmail)), '.jpg?s=', aSize)
			INTO link;
	END IF;
		
	RETURN link;		
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to show/display details of an account/user.
--
DROP PROCEDURE IF EXISTS {$db->_['PGetAccountDetails']};
CREATE PROCEDURE {$db->_['PGetAccountDetails']}
(
	IN aUserId INT
)
BEGIN
	
	SELECT 
		U.accountUser AS account,
		U.nameUser AS name,
		U.emailUser AS email,
		U.avatarUser AS avatar,
		U.gravatarUser AS gravatar,
		{$db->_['FGetGravatarLinkFromEmail']}(U.gravatarUser, 60) AS gravatarsmall,
		{$db->_['FGetGravatarLinkFromEmail']}(U.gravatarUser, 15) AS gravatarmicro,
		G.idGroup AS groupakronym,
		G.nameGroup AS groupdesc
	FROM {$db->_['User']} AS U
		INNER JOIN {$tGroupMember} AS Gm
			ON U.idUser = Gm.GroupMember_idUser
		INNER JOIN {$tGroup} AS G
			ON G.idGroup = Gm.GroupMember_idGroup
	WHERE
		U.idUser = aUserId
	;
	
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to change email for an account/user.
--
-- Returns the number of affected rows as an OUT parameter.
--
DROP PROCEDURE IF EXISTS {$db->_['PChangeAccountEmail']};
CREATE PROCEDURE {$db->_['PChangeAccountEmail']}
(
	IN aUserId INT,
	IN aEmail CHAR(100),
	OUT aRowsAffected INT
)
BEGIN
	
	UPDATE 
		{$db->_['User']}
	SET 
		emailUser = TRIM(aEmail)
	WHERE
		idUser = aUserId
	LIMIT 1
	;

	SELECT ROW_COUNT() INTO aRowsAffected;
	
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to change avatar for an account/user.
--
DROP PROCEDURE IF EXISTS {$db->_['PChangeAccountAvatar']};
CREATE PROCEDURE {$db->_['PChangeAccountAvatar']}
(
	IN aUserId INT,
	IN aAvatar CHAR(255)
)
BEGIN
	
	UPDATE 
		{$db->_['User']}
	SET 
		avatarUser = TRIM(aAvatar)
	WHERE
		idUser = aUserId
	LIMIT 1
	;
		
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to change gravatar for an account/user.
--
DROP PROCEDURE IF EXISTS {$db->_['PChangeAccountGravatar']};
CREATE PROCEDURE {$db->_['PChangeAccountGravatar']}
(
	IN aUserId INT,
	IN aGravatar CHAR(255)
)
BEGIN
	
	UPDATE 
		{$db->_['User']}
	SET 
		gravatarUser = TRIM(aGravatar)
	WHERE
		idUser = aUserId
	LIMIT 1
	;
	
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to create an account/user.
--
-- aMethod is the hashing-algoritm to be used for storing the password.
-- Review the funktion "{$db->_['FCreatePassword']}" to see which alternatives that are supported.
--
DROP PROCEDURE IF EXISTS {$db->_['PCreateAccount']};
CREATE PROCEDURE {$db->_['PCreateAccount']}
(
	OUT aUserId INT,
	IN aUserAccount CHAR(20),
	IN aPassword CHAR(32),
	IN aMethod CHAR(5),
	OUT aStatus INT
)
BEGIN
	DECLARE salt BINARY(10);
	
	--
	-- Check if the username exists, then set error code
	--
	SELECT idUser INTO aUserId FROM {$db->_['User']} WHERE accountUser = aUserAccount;
	
	IF aUserId IS NOT NULL THEN
	BEGIN
		SET aStatus = 1; -- FAILED, the name already exists
	END;
	
	--
	-- Else insert the new user
	--
	ELSE
	BEGIN

		--
		-- Insert the user account
		--
		SELECT BINARY(UNIX_TIMESTAMP(NOW())) INTO salt;
		
		INSERT INTO {$db->_['User']} 
			(accountUser, saltUser, passwordUser, methodUser, avatarUser)
		VALUES 
			(aUserAccount, salt, {$db->_['FCreatePassword']}(salt, aPassword, aMethod), aMethod, '{$imageLink}/man_60x60.png')
		;

		SET aUserId = LAST_INSERT_ID();
	
		--
		-- Insert default group memberships
		--
		INSERT INTO {$tGroupMember} 
			(GroupMember_idUser, GroupMember_idGroup) 
		VALUES 
			(aUserId, 'usr')
		;
	
		SET aStatus = 0; -- SUCCESS
	
	END;
	END IF;

END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to change password for an account/user.
--
DROP PROCEDURE IF EXISTS {$db->_['PChangeAccountPassword']};
CREATE PROCEDURE {$db->_['PChangeAccountPassword']}
(
	IN aUserId INT,
	IN aPassword CHAR(32),
	OUT aRowsAffected INT
)
BEGIN
	
	UPDATE 
		{$db->_['User']}
	SET 
		saltUser			= BINARY(UNIX_TIMESTAMP(NOW())),
		passwordUser 	= {$db->_['FCreatePassword']}(saltUser, aPassword, methodUser)
	WHERE
		idUser = aUserId
	LIMIT 1
	;

	SELECT ROW_COUNT() INTO aRowsAffected;
	
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to authenticate an account/user.
--
DROP PROCEDURE IF EXISTS {$db->_['PAuthenticateAccount']};
CREATE PROCEDURE {$db->_['PAuthenticateAccount']}
(
	OUT aUserId INT,
	IN aUserAccountOrEmail CHAR(100),
	IN aPassword CHAR(32),
	OUT aStatus INT
)
BEGIN

	--
	-- Check that account and passwords match
	--
	SELECT 
		idUser INTO aUserId 
	FROM {$db->_['User']} 
	WHERE 
		(
			accountUser		= aUserAccountOrEmail AND
			passwordUser	= {$db->_['FCreatePassword']}(saltUser, aPassword, methodUser)
		) OR
		(
			emailUser			= aUserAccountOrEmail AND
			passwordUser	= {$db->_['FCreatePassword']}(saltUser, aPassword, methodUser)
		)
	;
	
	IF aUserId IS NULL THEN
		SET aStatus = 1; -- FAILED, the account does not exists or passwords does not match.
	ELSE
		SET aStatus = 0; -- SUCCESS
	END IF;

END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Function to create a password from a salt, password and method.
--
DROP FUNCTION IF EXISTS {$db->_['FCreatePassword']};
CREATE FUNCTION {$db->_['FCreatePassword']}
(
	aSalt BINARY(10),
	aPassword CHAR(32),
	aMethod CHAR(5)
)
RETURNS BINARY(40)
BEGIN
	DECLARE password BINARY(40);
	
	--
	-- Switch on the method to be used
	--
	CASE TRIM(aMethod)
		WHEN 'MD5' 		THEN SELECT md5(CONCAT(aSalt, aPassword)) INTO password;
		WHEN 'SHA-1' 	THEN SELECT sha1(CONCAT(aSalt, aPassword)) INTO password;
		WHEN 'PLAIN' 	THEN SELECT aPassword INTO password;
	END CASE;
	
	RETURN password;
	
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Add default groups
--
INSERT INTO {$tGroup} (idGroup, nameGroup) VALUES ('adm', 'Administrators of the site');
INSERT INTO {$tGroup} (idGroup, nameGroup) VALUES ('usr', 'Regular users of the site');

EOD;

$hashingalgoritm = DB_PASSWORDHASHING;
$account 	= 'mikael';
$password	= 'hemligt';
$mail			= "mos@bth.se";
$avatar 	= "{$imageLink}man_60x60.png";

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
$avatar 	= "{$imageLink}woman_60x60.png";

$query .= <<<EOD
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Add default user(s) 
--
CALL {$db->_['PCreateAccount']}(@aUserId, '{$account}', '{$password}', '{$hashingalgoritm}', @aStatus);
CALL {$db->_['PChangeAccountEmail']}(@aUserId, '{$mail}', @ignore);
CALL {$db->_['PChangeAccountAvatar']}(@aUserId, '{$avatar}');


--
-- Add first user as adm groupmember
--
INSERT INTO {$tGroupMember} (GroupMember_idUser, GroupMember_idGroup) 
	VALUES (1, 'adm');


EOD;


?>