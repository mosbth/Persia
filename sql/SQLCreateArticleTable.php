<?php
// ===========================================================================================
//
// SQLCreateArticleTable.php
//
// SQL statements to create the tables for the Article tables.
//
// WARNING: Do not forget to check input variables for SQL injections. 
//
// Author: Mikael Roos
//


// Get the tablenames
$tArticle 		= DBT_Article;
$tUser 			= DBT_User;
$tGroup 		= DBT_Group;
$tGroupMember 	= DBT_GroupMember;
$tStatistics	= DBT_Statistics;

// Get the SP names
$spPGetArticleDetailsAndArticleList	= DBSP_PGetArticleDetailsAndArticleList;
$spPGetArticleDetails				= DBSP_PGetArticleDetails;
$spPInsertOrUpdateArticle			= DBSP_PInsertOrUpdateArticle;

// Get the UDF names
$udfFCheckUserIsOwnerOrAdmin	= DBUDF_FCheckUserIsOwnerOrAdmin;

// Get the trigger names
$trAddArticle					= DBTR_TAddArticle;

// Create the query
$query = <<<EOD

--
-- Table for the Article
--
DROP TABLE IF EXISTS {$tArticle};
CREATE TABLE {$tArticle} (

  -- Primary key(s)
  idArticle INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

  -- Foreign keys
  Article_idUser INT NOT NULL,
  FOREIGN KEY (Article_idUser) REFERENCES {$tUser}(idUser),
  
  -- Attributes
  titleArticle VARCHAR(256) NOT NULL,
  contentArticle BLOB NOT NULL,
  createdArticle DATETIME NOT NULL,
  modifiedArticle DATETIME NULL,
  deletedArticle DATETIME NULL
);


--
-- SP to insert or update article
-- If article id is 0 then insert, else update
--
DROP PROCEDURE IF EXISTS {$spPInsertOrUpdateArticle};
CREATE PROCEDURE {$spPInsertOrUpdateArticle}
(
	INOUT aArticleId INT, 
	IN aUserId INT, 
	IN aTitle VARCHAR(256), 
	IN aContent BLOB
)
BEGIN
	IF aArticleId = 0 THEN
	BEGIN
		INSERT INTO {$tArticle}	
			(Article_idUser, titleArticle, contentArticle, createdArticle) 
			VALUES 
			(aUserId, aTitle, aContent, NOW());
		SET aArticleId = LAST_INSERT_ID();
	END;
	ELSE
	BEGIN
		UPDATE {$tArticle} SET
			titleArticle 	= aTitle,
			contentArticle 	= aContent,
			modifiedArticle	= NOW()
		WHERE
			idArticle = aArticleId  AND
			{$udfFCheckUserIsOwnerOrAdmin}(aArticleId, aUserId)
		LIMIT 1;
	END;
	END IF;
END;


--
-- SP to get the contents of an article
--
DROP PROCEDURE IF EXISTS {$spPGetArticleDetails};
CREATE PROCEDURE {$spPGetArticleDetails}
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
	FROM {$tArticle} AS A
		INNER JOIN {$tUser} AS U
			ON A.Article_idUser = U.idUser
	WHERE
		idArticle = aArticleId AND
		deletedArticle IS NULL AND
		{$udfFCheckUserIsOwnerOrAdmin}(aArticleId, aUserId);
END;


--
-- SP to get the contents of an article and provide a list of the latest articles 
--
-- Limit does not accept a varible
-- http://bugs.mysql.com/bug.php?id=11918
--
DROP PROCEDURE IF EXISTS {$spPGetArticleDetailsAndArticleList};
CREATE PROCEDURE {$spPGetArticleDetailsAndArticleList}
(
	IN aArticleId INT, 
	IN aUserId INT
)
BEGIN
	CALL {$spPGetArticleDetails}(aArticleId, aUserId);

	SELECT 
		idArticle AS id,
		titleArticle AS title,
		COALESCE(modifiedArticle, createdArticle) AS latest
	FROM {$tArticle}
	WHERE
		Article_idUser = aUserId AND 
		deletedArticle IS NULL
	ORDER BY modifiedArticle, createdArticle
	LIMIT 20;
END;


--
--  Create UDF that checks if user owns article or is member of group adm.
--
DROP FUNCTION IF EXISTS {$udfFCheckUserIsOwnerOrAdmin};
CREATE FUNCTION {$udfFCheckUserIsOwnerOrAdmin}
(
	aArticleId INT,
	aUserId INT
)
RETURNS BOOLEAN
BEGIN
	DECLARE isAdmin INT;
	DECLARE isOwner INT;
	
	SELECT idUser INTO isAdmin
	FROM {$tUser} AS U
		INNER JOIN {$tGroupMember} AS GM
			ON U.idUser = GM.GroupMember_idUser
		INNER JOIN {$tGroup} AS G
			ON G.idGroup = GM.GroupMember_idGroup
	WHERE
		idGroup = 'adm' AND
		idUser = aUserId;

	SELECT idUser INTO isOwner
	FROM {$tUser} AS U
		INNER JOIN {$tArticle} AS A
			ON U.idUser = A.Article_idUser
	WHERE
		idArticle = aArticleId AND
		idUser = aUserId;
		
	RETURN (isAdmin OR isOwner);		
END;


--
-- Create trigger for Statistics
-- Add +1 when new article is created
--
DROP TRIGGER IF EXISTS {$trAddArticle};
CREATE TRIGGER {$trAddArticle}
AFTER INSERT ON {$tArticle}
FOR EACH ROW
BEGIN
  UPDATE {$tStatistics} 
  SET 
  	numOfArticlesStatistics = numOfArticlesStatistics + 1
  WHERE 
  	Statistics_idUser = NEW.Article_idUser;
END;



EOD;


?>