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
	deletedFile DATETIME NULL

);


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


EOD;


?>