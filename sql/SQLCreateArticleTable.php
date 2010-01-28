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
DROP PROCEDURE IF EXISTS PInsertOrUpdateArticle;
CREATE PROCEDURE PInsertOrUpdateArticle
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
			idArticle = aArticleId AND
			Article_idUser = aUserId
		LIMIT 1;
	END;
	END IF;
END;


--
-- SP to get the contents of an article
--
DROP PROCEDURE IF EXISTS PGetArticleDetails;
CREATE PROCEDURE PGetArticleDetails
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
		Article_idUser = aUserId AND
		deletedArticle IS NULL;
END;


--
-- SP to get the contents of an article and provide a list of the latest articles 
--
-- Limit does not accept a varible
-- http://bugs.mysql.com/bug.php?id=11918
--
DROP PROCEDURE IF EXISTS PGetArticleDetailsAndArticleList;
CREATE PROCEDURE PGetArticleDetailsAndArticleList
(
	IN aArticleId INT, 
	IN aUserId INT
)
BEGIN
	CALL PGetArticleDetails(aArticleId, aUserId);

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

EOD;


?>