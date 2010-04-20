<?php
// ===========================================================================================
//
// SQLCreateUserAndGroupTables.php
//
// SQL statements to creta the tables for the User and group tables.
//
// WARNING: Do not forget to check input variables for SQL injections. 
//
// Author: Mikael Roos
//


// Get the tablenames
$tUser 				= DBT_User;
$tGroup 			= DBT_Group;
$tGroupMember = DBT_GroupMember;
$tStatistics 	= DBT_Statistics;

// Get the SP/UDF/trigger names
$trInsertUser			= DBTR_TInsertUser;
$spAccountDetails = DBSP_PGetAccountDetails;
$spChangePassword	= DBSP_PChangeAccountPassword;
$spChangeEmail		= DBSP_PChangeAccountEmail;
$spChangeAvatar		= DBSP_PChangeAccountAvatar;

$imageLink = WS_IMAGES;

// Create the query
$query = <<<EOD
-- =============================================================================================
--
-- SQL for User
--
-- =============================================================================================

-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Drop all tables first
--
DROP TABLE IF EXISTS {$tGroupMember};
DROP TABLE IF EXISTS {$tUser};
DROP TABLE IF EXISTS {$tGroup};


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Table for the User
--
CREATE TABLE {$tUser} (

  -- Primary key(s)
  idUser INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

  -- Attributes
  accountUser CHAR(20) NOT NULL UNIQUE,
  nameUser CHAR(100) NOT NULL,
  emailUser CHAR(100) NOT NULL,
  passwordUser CHAR(32) NOT NULL,
  avatarUser VARCHAR(255) NULL
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
  GroupMember_idUser INT NOT NULL,
  GroupMember_idGroup CHAR(3) NOT NULL,
	
  FOREIGN KEY (GroupMember_idUser) REFERENCES {$tUser}(idUser),
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
  Statistics_idUser INT NOT NULL,
	
  FOREIGN KEY (Statistics_idUser) REFERENCES {$tUser}(idUser),
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
AFTER INSERT ON {$tUser}
FOR EACH ROW
BEGIN
  INSERT INTO {$tStatistics} (Statistics_idUser) VALUES (NEW.idUser);
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to show/display details of an account/user.
--
DROP PROCEDURE IF EXISTS {$spAccountDetails};
CREATE PROCEDURE {$spAccountDetails}
(
	IN aUserId INT
)
BEGIN
	
	SELECT 
		U.accountUser AS account,
		U.nameUser AS name,
		U.emailUser AS email,
		U.avatarUser AS avatar,
		G.idGroup AS groupakronym,
		G.nameGroup AS groupdesc
	FROM $tUser AS U
		INNER JOIN {$tGroupMember} AS Gm
			ON U.idUser = Gm.GroupMember_idUser
		INNER JOIN {$tGroup} AS G
			ON G.idGroup = Gm.GroupMember_idGroup
	;
	
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to change password for an account/user.
--
DROP PROCEDURE IF EXISTS {$spChangePassword};
CREATE PROCEDURE {$spChangePassword}
(
	IN aUserId INT,
	IN aPassword CHAR(32)
)
BEGIN
	
	UPDATE 
		$tUser
	SET 
		passwordUser = md5(aPassword)
	WHERE
		idUser = aUserId
	LIMIT 1
	;
	
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to change email for an account/user.
--
DROP PROCEDURE IF EXISTS {$spChangeEmail};
CREATE PROCEDURE {$spChangeEmail}
(
	IN aUserId INT,
	IN aEmail CHAR(100)
)
BEGIN
	
	UPDATE 
		$tUser
	SET 
		emailUser = aEmail
	WHERE
		idUser = aUserId
	LIMIT 1
	;
	
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to change password for an account/user.
--
DROP PROCEDURE IF EXISTS {$spChangeAvatar};
CREATE PROCEDURE {$spChangeAvatar}
(
	IN aUserId INT,
	IN aAvatar CHAR(255)
)
BEGIN
	
	UPDATE 
		$tUser
	SET 
		avatarUser = aAvatar
	WHERE
		idUser = aUserId
	LIMIT 1
	;
	
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Add default user(s) 
--
INSERT INTO {$tUser} (accountUser, emailUser, nameUser, passwordUser, avatarUser)
VALUES ('mikael', 'mos@bth.se', 'Mikael Roos', md5('hemligt'), '{$imageLink}/man_60x60.png');
INSERT INTO {$tUser} (accountUser, emailUser, nameUser, passwordUser, avatarUser)
VALUES ('doe', 'doe@bth.se', 'John/Jane Doe', md5('doe'), '{$imageLink}/woman_60x60.png');


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Add default groups
--
INSERT INTO {$tGroup} (idGroup, nameGroup) VALUES ('adm', 'Administrators of the site');
INSERT INTO {$tGroup} (idGroup, nameGroup) VALUES ('usr', 'Regular users of the site');


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Add default groupmembers
--
INSERT INTO {$tGroupMember} (GroupMember_idUser, GroupMember_idGroup) 
	VALUES ((SELECT idUser FROM {$tUser} WHERE accountUser = 'doe'), 'usr');
INSERT INTO {$tGroupMember} (GroupMember_idUser, GroupMember_idGroup) 
	VALUES ((SELECT idUser FROM {$tUser} WHERE accountUser = 'mikael'), 'adm');


EOD;


?>