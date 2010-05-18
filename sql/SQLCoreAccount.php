<?php
// ===========================================================================================
//
// File: SQLCoreAccount.php
//
// Description: SQL statements to create the tables for the User and group tables.
//
// Author: Mikael Roos
//

// Get (or create) an instance of the database object.
$db = CDatabaseController::GetInstance();

// Link to images
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
DROP TABLE IF EXISTS {$db->_['GroupMember']};
DROP TABLE IF EXISTS {$db->_['User']};
DROP TABLE IF EXISTS {$db->_['Group']};


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

  -- Attributes related to resetting the password
  key3User BINARY(32) NULL UNIQUE,
  expireUser DATETIME NULL,

	-- Attributes for user profile info
  avatarUser VARCHAR(255) NULL,
  gravatarUser VARCHAR(100) NULL
);


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Table for the Group
--
CREATE TABLE {$db->_['Group']} (

  -- Primary key(s)
  idGroup CHAR(3) NOT NULL PRIMARY KEY,

  -- Attributes
  nameGroup CHAR(40) NOT NULL
);


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Table for the GroupMember
--
CREATE TABLE {$db->_['GroupMember']} (

  -- Primary key(s)
  --
  -- The PK is the combination of the two foreign keys, see below.
  --
  
  -- Foreign keys
  GroupMember_idUser INT UNSIGNED NOT NULL,
  GroupMember_idGroup CHAR(3) NOT NULL,
	
  FOREIGN KEY (GroupMember_idUser) REFERENCES {$db->_['User']}(idUser),
  FOREIGN KEY (GroupMember_idGroup) REFERENCES {$db->_['Group']}(idGroup),

  PRIMARY KEY (GroupMember_idUser, GroupMember_idGroup)
  
  -- Attributes

);


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Table for the Statistics
--
DROP TABLE IF EXISTS {$db->_['Statistics']};
CREATE TABLE {$db->_['Statistics']} (

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
DROP TRIGGER IF EXISTS {$db->_['TInsertUser']};
CREATE TRIGGER {$db->_['TInsertUser']}
AFTER INSERT ON {$db->_['User']}
FOR EACH ROW
BEGIN
  INSERT INTO {$db->_['Statistics']} (Statistics_idUser) VALUES (NEW.idUser);
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
-- Function to return a link to the users avatar or gravatar.
-- First check if gravatar exists, then check avatar. If neither exists just return an 
-- unknown figure.
--
DROP FUNCTION IF EXISTS {$db->_['FGetAvatar']};
CREATE FUNCTION {$db->_['FGetAvatar']}
(
	aUserId INT UNSIGNED,
	aSize INT
)
RETURNS CHAR(255)
BEGIN
	DECLARE link1 CHAR(255);
	DECLARE link2 CHAR(255);
	DECLARE link CHAR(255);

	-- Get avatars from user table
	SELECT 
		avatarUser, {$db->_['FGetGravatarLinkFromEmail']}(gravatarUser, aSize) 
	INTO 
		link1, link2 
	FROM
		{$db->_['User']}
	WHERE 
		idUser = aUserId;
	
	-- Has gravatar?
	IF link2 != '' THEN
	BEGIN
		SET link = link2;
	END;
	ELSEIF link1 != '' THEN
	BEGIN
		SET link = link1;
	END;
	ELSE
	BEGIN
		SET link = CONCAT('{$imageLink}', '/egg_60x60.png');
	END;
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
		INNER JOIN {$db->_['GroupMember']} AS Gm
			ON U.idUser = Gm.GroupMember_idUser
		INNER JOIN {$db->_['Group']} AS G
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
-- SP to get mail adress of an account/user.
--
-- aAccountOrMail: Be the account name or a mailadress.
-- aAccount: The accountname that matched.
-- aMail: The resulting mail, if found, else empty.
-- aStatus: 0 Success
--          1 Account has no mail
--          2 No such account nor mail found
-- 
DROP PROCEDURE IF EXISTS {$db->_['PGetMailAdressFromAccount']};
CREATE PROCEDURE {$db->_['PGetMailAdressFromAccount']}
(
	IN aAccountOrMail CHAR(100),
	OUT aAccount CHAR(32),
	OUT aMail CHAR(100),
	OUT aStatus INT
)
BEGIN
	DECLARE mailByAccount CHAR(100);
	DECLARE mailByMail CHAR(100);
	DECLARE accountByAccount CHAR(32);
	DECLARE accountByMail CHAR(32);

	-- Is it an accountname with mail?
	SELECT
		accountUser, emailUser INTO accountByAccount, mailByAccount 
	FROM {$db->_['User']} 
	WHERE 
		accountUser = aAccountOrMail;
	
	-- Is it an mailadress which exists together with an account?
	SELECT 
		accountUser, emailUser INTO accountByMail, mailByMail 
	FROM {$db->_['User']} 
	WHERE 
		emailUser = aAccountOrMail;
	
	-- Get the correct status to return
	IF mailByAccount IS NOT NULL THEN
	BEGIN
		SET aAccount 	= accountByAccount;
		SET aMail 		= mailByAccount;
		SET aStatus 	= 0;
	END;
	ELSEIF mailByAccount IS NULL AND accountByAccount IS NOT NULL THEN
	BEGIN
		SET aAccount 	= accountByAccount;
		SET aMail 		= NULL;
		SET aStatus 	= 1;
	END;
	ELSEIF mailByMail IS NOT NULL THEN
	BEGIN
		SET aAccount 	= accountByMail;
		SET aMail 		= mailByMail;
		SET aStatus	 	= 0;
	END;
	ELSE
	BEGIN
		SET aAccount 	= NULL;
		SET aMail 		= NULL;
		SET aStatus 	= 2;
	END;
	END IF;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to initiate password reset by saving a key with the user.
-- By sending this same key to {$db->_['PPasswordResetActivate']} will allow to
-- reset the password.
--
-- aKey should have a value initiated by the caller (key1).
-- The procedure creates a new key (key2) and uses these two keys to generate a third key (key3). 
-- Key3 is stored in the user table.
-- Key2 is put in the aKey OUT variable. Both key1 and key2 are later needed to carry out the 
-- password reset action using {$db->_['PPasswordResetActivate']}.
--
-- A procedure could work like this:
-- Create key1 in PHP using some random value and create a MD5 hash from it.
-- The procedure creates key2 using similare techniques.
-- Key3 is created by hashing a combination of key1 and key2.
-- Key3 is stored in the database.
-- Key2 is sent to the user via mail.
-- Key1 is stored in the webbapplications session.
-- The user takes key2 from the mail and inputs it in a form. 
-- The webbapplikation takes key2 from the form and key1 from the session and sends it as 
-- input to the procedure {$db->_['PPasswordResetActivate']} which resets the password.
--
-- I'm not really sure on the advantages with this but if feels better than just using 1 key
-- and sending it in plain text to the user. This could, of course, be further evaluated. 
--
DROP PROCEDURE IF EXISTS {$db->_['PPasswordResetGetKey']};
CREATE PROCEDURE {$db->_['PPasswordResetGetKey']}
(
	IN aAccountUser CHAR(32),
	INOUT aKey CHAR(32)
)
BEGIN
	DECLARE key1 BINARY(32);
	DECLARE key2 BINARY(32);
	DECLARE key3 BINARY(32);
	
	SET key1 = aKey;
	SET key2 = BINARY(MD5(UNIX_TIMESTAMP(NOW())));
	SET key3 = MD5(CONCAT(key1,key2));
	SET aKey = key2;
	
	UPDATE 
		{$db->_['User']}
	SET 
		key3User 		= key3,
		expireUser 	= ADDTIME(NOW(), '01:00:00')
	WHERE
		accountUser = aAccountUser
	LIMIT 1;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to carry out a password reset request. It is really a alternative way of loggin in
-- and authenticating a user based on a token.
--
-- aStatus 0 = Success
-- aStatus 1 = Time expired for key3
-- aStatus 2 = No match
--
DROP PROCEDURE IF EXISTS {$db->_['PPasswordResetActivate']};
CREATE PROCEDURE {$db->_['PPasswordResetActivate']}
(
	OUT aAccountId INT UNSIGNED,
	OUT aAccountName CHAR(32),
	IN aKey1 CHAR(32),
	IN aKey2 CHAR(32),
	OUT aStatus INT
)
BEGIN
	DECLARE key1 BINARY(32);
	DECLARE key2 BINARY(32);
	DECLARE key3 BINARY(32);
	DECLARE expire DATETIME;
	
	SET key1 = aKey1;
	SET key2 = aKey2;
	SET key3 = MD5(CONCAT(key1,key2));

	-- Find the key
	SELECT 
		idUser, accountUser, expireUser INTO aAccountId, aAccountName, expire
	FROM 
		{$db->_['User']}
	WHERE
		key3User 		= key3 AND
		expireUser 	> NOW();
		
	-- Clean up and set correct error messages
	IF aAccountId IS NOT NULL THEN
	BEGIN
		-- Reset the key
		UPDATE 
			{$db->_['User']}
		SET 
			key3User 		= NULL,
			expireUser 	= NULL
		WHERE
			idUser = aAccountId
		LIMIT 1;

		SET aStatus = 0;
	END;
	ELSEIF expire > NOW() THEN
	BEGIN
		SET aStatus = 1;
	END;
	ELSE
	BEGIN
		SET aStatus = 2;
	END;
	END IF;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to get account id from account name. If the name does not exists then create a new account
-- for that account name.
-- This is ordinary used to silently create new accounts when using an external authentication
-- server, for example LDAP or OpenID.
--
-- aUserId is the id of the user, existing or created.
-- aUserAccount is the name of the account.
--
DROP PROCEDURE IF EXISTS {$db->_['PGetOrCreateAccountId']};
CREATE PROCEDURE {$db->_['PGetOrCreateAccountId']}
(
	OUT aUserId INT,
	IN aUserAccount CHAR(32),
	IN aMail CHAR(100)
)
BEGIN
	DECLARE status INT;
	
	-- Get account id
	SELECT idUser INTO aUserId FROM {$db->_['User']} WHERE accountUser = aUserAccount;

	-- Create user if it does not exists
	IF aUserId IS NULL THEN
	BEGIN
		CALL {$db->_['PCreateAccount']}(aUserId, aUserAccount, aUserAccount, 'PLAIN', status);
		CALL {$db->_['PChangeAccountEmail']}(aUserId, aMail, status);
	END;
	END IF;

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
	IN aUserAccount CHAR(32),
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
		INSERT INTO {$db->_['GroupMember']} 
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
INSERT INTO {$db->_['Group']} (idGroup, nameGroup) VALUES ('adm', 'Administrators of the site');
INSERT INTO {$db->_['Group']} (idGroup, nameGroup) VALUES ('usr', 'Regular users of the site');

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
INSERT INTO {$db->_['GroupMember']} (GroupMember_idUser, GroupMember_idGroup) 
	VALUES (1, 'adm');


EOD;


?>