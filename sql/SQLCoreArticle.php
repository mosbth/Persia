<?php
// ===========================================================================================
//
// File: SQLCoreArticle.php
//
// Description: SQL statements to create the tables for the Article tables.
//
// Author: Mikael Roos
//

// Get (or create) an instance of the database object.
$db = CDatabaseController::GetInstance();

// Create the query
$query = <<<EOD

-- =============================================================================================
--
-- SQL for Article
--
-- =============================================================================================


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Table for the Article
--
DROP TABLE IF EXISTS {$db->_['Article']};
CREATE TABLE {$db->_['Article']} (

  -- Primary key(s)
  idArticle INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

  -- Foreign keys
  Article_idUser INT NOT NULL,
  FOREIGN KEY (Article_idUser) REFERENCES {$db->_['User']}(idUser),

  titleArticle VARCHAR(256) NULL,
  contentArticle BLOB NULL,
  createdArticle DATETIME NOT NULL,
  modifiedArticle DATETIME NULL,
  deletedArticle DATETIME NULL,

	-- Attributes to enable draft, publish and autosaves
  draftTitleArticle VARCHAR(256) NULL,
  draftContentArticle BLOB NULL,
  draftModifiedArticle DATETIME NULL,
  publishedArticle DATETIME NULL

);


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to insert or update article
-- If article id is 0 then insert, else update
--
DROP PROCEDURE IF EXISTS {$db->_['PInsertOrUpdateArticle']};
CREATE PROCEDURE {$db->_['PInsertOrUpdateArticle']}
(
	INOUT aArticleId INT, 
	IN aUserId INT, 
	IN aTitle VARCHAR(256), 
	IN aContent BLOB
)
BEGIN
	IF aArticleId = 0 THEN
	BEGIN
		INSERT INTO {$db->_['Article']}	
			(Article_idUser, titleArticle, contentArticle, createdArticle) 
			VALUES 
			(aUserId, aTitle, aContent, NOW());
		SET aArticleId = LAST_INSERT_ID();
	END;
	ELSE
	BEGIN
		UPDATE {$db->_['Article']} SET
			titleArticle 	= aTitle,
			contentArticle 	= aContent,
			modifiedArticle	= NOW()
		WHERE
			idArticle = aArticleId  AND
			{$db->_['FCheckUserIsOwnerOrAdmin']}(aArticleId, aUserId)
		LIMIT 1;
	END;
	END IF;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to get the contents of an article
--
DROP PROCEDURE IF EXISTS {$db->_['PGetArticleDetails']};
CREATE PROCEDURE {$db->_['PGetArticleDetails']}
(
	IN aArticleId INT, 
	IN aUserId INT
)
BEGIN
	SELECT 
		A.titleArticle AS title,
		A.contentArticle AS content,
		A.createdArticle AS created,
		A.modifiedArticle AS modified,
		COALESCE(A.modifiedArticle, A.createdArticle) AS latest,
		U.nameUser AS username		
	FROM {$db->_['Article']} AS A
		INNER JOIN {$db->_['User']} AS U
			ON A.Article_idUser = U.idUser
	WHERE
		idArticle = aArticleId AND
		deletedArticle IS NULL AND
		{$db->_['FCheckUserIsOwnerOrAdmin']}(aArticleId, aUserId);
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to provide a list of the latest articles 
--
-- Limit does not accept a varible
-- http://bugs.mysql.com/bug.php?id=11918
--
DROP PROCEDURE IF EXISTS {$db->_['PGetArticleList']};
CREATE PROCEDURE {$db->_['PGetArticleList']}
(
	IN aUserId INT
)
BEGIN
	SELECT 
		idArticle AS id,
		titleArticle AS title,
		COALESCE(modifiedArticle, createdArticle) AS latest
	FROM {$db->_['Article']}
	WHERE
		Article_idUser = aUserId AND 
		deletedArticle IS NULL
	ORDER BY modifiedArticle, createdArticle
	LIMIT 20;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to get the contents of an article and provide a list of the latest articles 
--
DROP PROCEDURE IF EXISTS {$db->_['PGetArticleDetailsAndArticleList']};
CREATE PROCEDURE {$db->_['PGetArticleDetailsAndArticleList']}
(
	IN aArticleId INT, 
	IN aUserId INT
)
BEGIN
	CALL {$db->_['PGetArticleDetails']}(aArticleId, aUserId);
	CALL {$db->_['PGetArticleList']}(aUserId);
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
--  Create UDF that checks if user owns article or is member of group adm.
--
DROP FUNCTION IF EXISTS {$db->_['FCheckUserIsOwnerOrAdmin']};
CREATE FUNCTION {$db->_['FCheckUserIsOwnerOrAdmin']}
(
	aArticleId INT,
	aUserId INT
)
RETURNS BOOLEAN
BEGIN
	DECLARE isAdmin INT;
	DECLARE isOwner INT;
	
	SELECT idUser INTO isAdmin
	FROM {$db->_['User']} AS U
		INNER JOIN {$db->_['GroupMember']} AS GM
			ON U.idUser = GM.GroupMember_idUser
		INNER JOIN {$db->_['Group']} AS G
			ON G.idGroup = GM.GroupMember_idGroup
	WHERE
		idGroup = 'adm' AND
		idUser = aUserId;

	SELECT idUser INTO isOwner
	FROM {$db->_['User']} AS U
		INNER JOIN {$db->_['Article']} AS A
			ON U.idUser = A.Article_idUser
	WHERE
		idArticle = aArticleId AND
		idUser = aUserId;
		
	RETURN (isAdmin OR isOwner);		
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Create trigger for Statistics
-- Add +1 when new article is created
--
DROP TRIGGER IF EXISTS {$db->_['TAddArticle']};
CREATE TRIGGER {$db->_['TAddArticle']}
AFTER INSERT ON {$db->_['Article']}
FOR EACH ROW
BEGIN
  UPDATE {$db->_['Statistics']} 
  SET 
  	numOfArticlesStatistics = numOfArticlesStatistics + 1
  WHERE 
  	Statistics_idUser = NEW.Article_idUser;
END;


EOD;


?>