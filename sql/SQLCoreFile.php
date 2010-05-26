<?php
// ===========================================================================================
//
// File: SQLCoreFile.php
//
// Description: SQL statements for storing files.
//
// Author: Mikael Roos, mos@bth.se
//

// Get (or create) an instance of the database object.
$db = CDatabaseController::GetInstance();

// Create the query
$query = <<<EOD

-- =============================================================================================
--
-- SQL for File
--
-- =============================================================================================


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Table for File
--
-- uniqueNameFile must be unique in combination with the userid.
--
DROP TABLE IF EXISTS {$db->_['File']};
CREATE TABLE {$db->_['File']} (

	-- Primary key(s)
	idFile INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
	
	-- Foreign keys
	File_idUser INT UNSIGNED NOT NULL,
	FOREIGN KEY (File_idUser) REFERENCES {$db->_['User']}(idUser),
	
	-- Attributes
	nameFile VARCHAR({$db->_['CSizeFileName']}) NOT NULL,
	uniqueNameFile VARCHAR({$db->_['CSizeFileNameUnique']}) NOT NULL,
	pathToDiskFile VARCHAR({$db->_['CSizePathToDisk']}) NOT NULL,
	sizeFile INT UNSIGNED NOT NULL,
	mimetypeFile VARCHAR({$db->_['CSizeMimetype']}) NOT NULL,
	createdFile DATETIME NOT NULL,
	modifiedFile DATETIME NULL,
	deletedFile DATETIME NULL,

	-- Index
	INDEX (uniqueNameFile)

) ENGINE MyISAM CHARACTER SET {$db->_['DefaultCharacterSet']} COLLATE {$db->_['DefaultCollate']};


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to insert new file
--
DROP PROCEDURE IF EXISTS {$db->_['PInsertFile']};
CREATE PROCEDURE {$db->_['PInsertFile']}
(
	IN aUserId INT UNSIGNED,
	IN aFilename VARCHAR({$db->_['CSizeFileName']}), 
	IN aUniqueFilename VARCHAR({$db->_['CSizeFileNameUnique']}), 
	IN aPathToDisk VARCHAR({$db->_['CSizePathToDisk']}), 
	IN aSize INT UNSIGNED,
	IN aMimetype VARCHAR({$db->_['CSizeMimetype']})
)
BEGIN
	INSERT INTO {$db->_['File']}	
		(File_idUser, nameFile, uniqueNameFile, pathToDiskFile, sizeFile, mimetypeFile, createdFile) 
		VALUES 
		(aUserId, aFilename, aUniqueFilename, aPathToDisk, aSize, aMimetype, NOW());
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to list all files
--
DROP PROCEDURE IF EXISTS {$db->_['PListFiles']};
CREATE PROCEDURE {$db->_['PListFiles']}
(
	IN aUserId INT UNSIGNED
)
BEGIN
	SELECT 
		File_idUser AS owner, 
		nameFile AS name, 
		uniqueNameFile AS uniquename,
		pathToDiskFile AS path, 
		sizeFile AS size, 
		mimetypeFile AS mimetype, 
		createdFile AS created,
		modifiedFile AS modified,
		deletedFile AS deleted
	FROM {$db->_['File']}
	WHERE
		File_idUser = aUserId AND
		deletedFile IS NULL;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Function to check if file exists and if user has permissions to us it.
-- Return values:
--  0 success
--  1 no permission to update file ({$db->_['FFileCheckPermissionMessages'][1]})
--  2 file does not exists  ({$db->_['FFileCheckPermissionMessages'][2]})
--
DROP FUNCTION IF EXISTS {$db->_['FFileCheckPermission']};
CREATE FUNCTION {$db->_['FFileCheckPermission']}
(
	aFileId INT UNSIGNED,
	aUserId INT UNSIGNED
)
RETURNS TINYINT UNSIGNED
BEGIN
	DECLARE i TINYINT UNSIGNED;
	
	-- File exists and user have permissions to update file?
	SELECT idFile INTO i FROM {$db->_['File']} 
	WHERE 
		idFile = aFileId AND
		(
			{$db->_['FCheckUserIsAdmin']}(aUserId) OR
			File_idUser = aUserId
		);
	IF i IS NOT NULL THEN
		RETURN 0;
	END IF;	

	-- Does file exists?
	SELECT idFile INTO i FROM {$db->_['File']} WHERE idFile = aFileId;
	IF i IS NULL THEN
		RETURN 2;
	END IF;

	-- So, file exists but user has no permissions to use/update file.
	RETURN 1;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to show details of a file
--
DROP PROCEDURE IF EXISTS {$db->_['PFileDetails']};
CREATE PROCEDURE {$db->_['PFileDetails']}
(
	IN aUserId INT UNSIGNED,
	IN aUniqueFilename VARCHAR({$db->_['CSizeFileNameUnique']}),
	OUT aSuccess TINYINT UNSIGNED	
)
BEGIN
	DECLARE fileid INT UNSIGNED;
	
	-- Get the id of the file
	SELECT idFile INTO fileid FROM {$db->_['File']}
	WHERE
		uniqueNameFile = aUniqueFilename AND
		File_idUser = aUserId;

	-- Check permissions
	SELECT {$db->_['FFileCheckPermission']}(fileid, aUserId) INTO aSuccess;
		
	-- Get details from file
	SELECT 
		idFile AS fileid, 
		File_idUser AS owner, 
		nameFile AS name, 
		uniqueNameFile AS uniquename,
		pathToDiskFile AS path, 
		sizeFile AS size, 
		mimetypeFile AS mimetype, 
		createdFile AS created,
		modifiedFile AS modified,
		deletedFile AS deleted
	FROM {$db->_['File']}
	WHERE
		uniqueNameFile = aUniqueFilename AND
		File_idUser = aUserId;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to update details of a file
-- The userid sent in must be owner or admin to be able to change the file details.
-- See funktion {$db->_['FFileCheckPermission']} for return values.
--
DROP PROCEDURE IF EXISTS {$db->_['PFileDetailsUpdate']};
CREATE PROCEDURE {$db->_['PFileDetailsUpdate']}
(
	IN aFileId INT UNSIGNED,
	IN aUserId INT UNSIGNED,
	IN aFilename VARCHAR({$db->_['CSizeFileName']}), 
	IN aMimetype VARCHAR({$db->_['CSizeMimetype']}),
	OUT aSuccess TINYINT UNSIGNED
)
BEGIN
	-- Check permissions
	SELECT {$db->_['FFileCheckPermission']}(aFileId, aUserId) INTO aSuccess;
	
	-- Do the update
	UPDATE {$db->_['File']}
	SET
		nameFile 			= aFilename,
		mimetypeFile 	= aMimetype,
		modifiedFile	= NOW()
	WHERE 
		idFile = aFileId AND
		(
			{$db->_['FCheckUserIsAdmin']}(aUserId) OR
			File_idUser = aUserId
		);
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to toggle delete/not deleted file
-- aDeleteOrRestore 1 (delete), 2 (no delete)
-- See funktion {$db->_['FFileCheckPermission']} for return values.
--
DROP PROCEDURE IF EXISTS {$db->_['PFileDetailsDeleted']};
CREATE PROCEDURE {$db->_['PFileDetailsDeleted']}
(
	IN aFileId INT UNSIGNED,
	IN aUserId INT UNSIGNED,
	IN aDeleteOrRestore INT UNSIGNED,
	OUT aSuccess TINYINT UNSIGNED	
)
wrap: BEGIN
	DECLARE value DATETIME;
	
	-- Check permissions
	SELECT {$db->_['FFileCheckPermission']}(aFileId, aUserId) INTO aSuccess;

	-- Set the value to be updated, depends on aDeleteOrRestore
	CASE aDeleteOrRestore 
		WHEN 1 THEN SET value = NOW();
		WHEN 2 THEN SET value = NULL;
		ELSE LEAVE wrap;
	END CASE;
	
	-- Do the update
	UPDATE {$db->_['File']}
	SET
		deletedFile	= value
	WHERE 
		idFile = aFileId AND
		(
			{$db->_['FCheckUserIsAdmin']}(aUserId) OR
			File_idUser = aUserId
		);
END wrap;


EOD;


?>