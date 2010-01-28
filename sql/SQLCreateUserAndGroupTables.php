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
$tUser 			= DBT_User;
$tGroup 		= DBT_Group;
$tGroupMember 	= DBT_GroupMember;

// Create the query
$query = <<<EOD
DROP TABLE IF EXISTS {$tUser};
DROP TABLE IF EXISTS {$tGroup};
DROP TABLE IF EXISTS {$tGroupMember};

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
  passwordUser CHAR(32) NOT NULL
);


--
-- Table for the Group
--
CREATE TABLE {$tGroup} (

  -- Primary key(s)
  idGroup CHAR(3) NOT NULL PRIMARY KEY,

  -- Attributes
  nameGroup CHAR(40) NOT NULL
);


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

--
-- Add default user(s) 
--
INSERT INTO {$tUser} (accountUser, emailUser, nameUser, passwordUser)
VALUES ('mikael', 'mos@bth.se', 'Mikael Roos', md5('hemligt'));
INSERT INTO {$tUser} (accountUser, emailUser, nameUser, passwordUser)
VALUES ('doe', 'doe@bth.se', 'John/Jane Doe', md5('doe'));

--
-- Add default groups
--
INSERT INTO {$tGroup} (idGroup, nameGroup) VALUES ('adm', 'Administrators of the site');
INSERT INTO {$tGroup} (idGroup, nameGroup) VALUES ('usr', 'Regular users of the site');

--
-- Add default groupmembers
--
INSERT INTO {$tGroupMember} (GroupMember_idUser, GroupMember_idGroup) 
	VALUES ((SELECT idUser FROM {$tUser} WHERE accountUser = 'doe'), 'usr');
INSERT INTO {$tGroupMember} (GroupMember_idUser, GroupMember_idGroup) 
	VALUES ((SELECT idUser FROM {$tUser} WHERE accountUser = 'mikael'), 'adm');

EOD;


?>