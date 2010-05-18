<?php
// ===========================================================================================
//
// File: SQLForumRomanum.php
//
// Description: SQL statements to create the tables for module ForumRomanum.
//
// Author: Mikael Roos
//

// Get (or create) an instance of the database object.
$db = CDatabaseController::GetInstance();

// Create the query
$query = <<<EOD

-- =============================================================================================
--
-- SQL for Forum
--
-- =============================================================================================


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Table for Topic
--
-- A forum topic. To connect a topic to all its post, look in table Topic2Post. However, the
-- first post is stored in the topic, for convinience and reduce of joins when looking for the
-- title of the topic (which is stored in the initial post).
--
DROP TABLE IF EXISTS {$db->_['Topic']};
CREATE TABLE {$db->_['Topic']} (

	--
	-- Primary key(s)
	--
	idTopic INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

	--
	-- Foreign keys
	--
	
	-- The first topic post
	Topic_idArticle INT NOT NULL,
	FOREIGN KEY (Topic_idArticle) REFERENCES {$db->_['Article']}(idArticle),
	
	-- Last person who posted in this topic
	lastPostByTopic INT NOT NULL,
	FOREIGN KEY (lastPostByTopic) REFERENCES {$db->_['User']}(idUser),
	  
	--
	-- Attributes
	--
	
	-- Counts the numer of posts in this topic
	counterTopic INT NOT NULL,
	
	-- Last time for posting to this topic
	lastPostWhenTopic DATETIME NOT NULL

);


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Table for Topic2Post
--
-- Connection between topics and posts. 
--
DROP TABLE IF EXISTS {$db->_['Topic2Post']};
CREATE TABLE {$db->_['Topic2Post']} (

	--
	-- Primary key(s)
	--
	-- Se below, combined from the two foreign keys

	--
	-- Foreign keys
	--
	
	-- The Topic
	Topic2Post_idTopic INT NOT NULL,
	FOREIGN KEY (Topic2Post_idTopic) REFERENCES {$db->_['Topic']}(idTopic),
  
	-- The Post
	Topic2Post_idArticle INT NOT NULL,
	FOREIGN KEY (Topic2Post_idArticle) REFERENCES {$db->_['Article']}(idArticle),

	-- Primary key(s)
	PRIMARY KEY (Topic2Post_idTopic, Topic2Post_idArticle)

	--
	-- Attributes
	--
	-- No additional attributes
	
);


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to get a list of all the topics together with details on each topic.
--
DROP PROCEDURE IF EXISTS {$db->_['PGetTopicList']};
CREATE PROCEDURE {$db->_['PGetTopicList']} ()
BEGIN
	SELECT 
		T.idTopic AS topicid,
		T.counterTopic AS postcounter,
		T.lastPostWhenTopic AS latest,
		A.idArticle AS postid,
		A.titleArticle AS title,
		A.createdArticle AS created,
		U.idUser AS userid,
		U.accountUser AS username,
		U1.accountUser AS latestby
	FROM {$db->_['Topic']} AS T
		INNER JOIN {$db->_['Article']} AS A
			ON T.Topic_idArticle = A.idArticle
		INNER JOIN {$db->_['User']} AS U
			ON A.Article_idUser = U.idUser
		INNER JOIN {$db->_['User']} AS U1
			ON T.lastPostByTopic = U1.idUser
	ORDER BY lastPostWhenTopic DESC
	;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to get details of a topic.
--
-- If aTopicId is set, use that.
-- If not, try to find a topic using the aPostId, as a second way to find the topic details.
--
DROP PROCEDURE IF EXISTS {$db->_['PGetTopicDetails']};
CREATE PROCEDURE {$db->_['PGetTopicDetails']}
(
	IN aTopicId INT,
	IN aPostId INT
)
BEGIN
	IF aTopicId = 0 THEN
	BEGIN
		SELECT Topic2Post_idTopic INTO aTopicId FROM {$db->_['Topic2Post']} WHERE Topic2Post_idArticle = aPostId;
	END;
	END IF;
	
	--
	-- Get the topic details
	--
	SELECT 
		T.idTopic AS topicid,
		T.counterTopic AS postcounter,
		T.lastPostWhenTopic AS lastpostwhen,
		T.Topic_idArticle AS toppost,
		A.titleArticle AS title,
		A.createdArticle AS created,
		A.modifiedArticle AS modified,
		U.accountUser AS creator,		
		U.idUser AS creatorid,
		U1.accountUser AS lastpostby
	FROM {$db->_['Topic']} AS T
		INNER JOIN {$db->_['Article']} AS A
			ON T.Topic_idArticle = A.idArticle
		INNER JOIN {$db->_['User']} AS U
			ON A.Article_idUser = U.idUser
		INNER JOIN {$db->_['User']} AS U1
			ON T.lastPostByTopic = U1.idUser
	WHERE
		T.idTopic = aTopicId 
	;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to get the content of a topic, both topic details and all the posts related to the topic.
--
DROP PROCEDURE IF EXISTS {$db->_['PGetTopicDetailsAndPosts']};
CREATE PROCEDURE {$db->_['PGetTopicDetailsAndPosts']}
(
	IN aTopicId INT
)
BEGIN
	--
	-- Get the topic details
	--
	CALL {$db->_['PGetTopicDetails']}(aTopicId, 0);
	
	--
	-- Get the list of all posts related to this topic
	--
	SELECT
		A.idArticle AS postid,
		A.titleArticle AS title,
		A.contentArticle AS content,
		A.createdArticle AS created,
		U.idUser AS userid,
		U.accountUser AS username,
		{$db->_['FGetAvatar']}(U.idUser, 60) AS avatar
	FROM {$db->_['Topic2Post']} AS T2P
		INNER JOIN {$db->_['Article']} AS A
			ON A.idArticle = T2P.Topic2Post_idArticle
		INNER JOIN {$db->_['User']} AS U
			ON A.Article_idUser = U.idUser
	WHERE 
		T2P.Topic2Post_idTopic = aTopicId AND
		A.deletedArticle IS NULL AND
		A.publishedArticle IS NOT NULL
	ORDER BY createdArticle ASC
	;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to get the details of a topic and a specific post.
--
DROP PROCEDURE IF EXISTS {$db->_['PGetPostDetails']};
CREATE PROCEDURE {$db->_['PGetPostDetails']}
(
	IN aPostId INT
)
BEGIN
	--
	-- Get the post details
	--
	SELECT
		A.idArticle AS postid,
		A.titleArticle AS title,
		A.contentArticle AS content,
		A.createdArticle AS created,
		A.modifiedArticle AS modified,
		A.deletedArticle AS deleted,
		A.publishedArticle AS published,
		IF(publishedArticle IS NULL, 0, 1) AS isPublished,
		IF(draftModifiedArticle IS NULL, 0, 1) AS hasDraft,
		A.draftTitleArticle AS draftTitle,
		A.draftContentArticle AS draftContent,
		A.draftModifiedArticle AS draftModified
	FROM {$db->_['Topic2Post']} AS T2P
		INNER JOIN {$db->_['Article']} AS A
			ON A.idArticle = T2P.Topic2Post_idArticle
		INNER JOIN {$db->_['User']} AS U
			ON A.Article_idUser = U.idUser
	WHERE 
		A.idArticle = aPostId AND
		A.deletedArticle IS NULL AND
		A.publishedArticle IS NOT NULL
	ORDER BY createdArticle ASC
	;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP for the first time when the post is published. Create/update topic tables accordingly.
--
-- If aTopicId is 0 then insert new entry into topic-table.
-- Keep tables Topic and Topic2Post updated.
--
DROP PROCEDURE IF EXISTS {$db->_['PInitialPostPublish']};
CREATE PROCEDURE {$db->_['PInitialPostPublish']}
(
	INOUT aTopicId INT,
	IN aPostId INT,
	IN aUserId INT
)
BEGIN	
	--
	-- Is it a new topic? Then create the topic else update it.
	--
	IF aTopicId = 0 THEN
	BEGIN
		INSERT INTO {$db->_['Topic']}	
			(Topic_idArticle, counterTopic, lastPostWhenTopic, lastPostByTopic) 
		VALUES 
			(aPostId, 1, NOW(), aUserId);
		SET aTopicId = LAST_INSERT_ID();
	END;
	
	--
	-- Topic exists, just update it
	--
	ELSE
	BEGIN
		UPDATE {$db->_['Topic']} SET
			counterTopic 			= counterTopic + 1,
			lastPostWhenTopic = NOW(), 
			lastPostByTopic		= aUserId
		WHERE 
			idTopic = aTopicId
		LIMIT 1;
	END;
	END IF;

	--
	-- First time this post is published, insert post entry in Topic2Post
	--
	INSERT INTO {$db->_['Topic2Post']}	
		(Topic2Post_idTopic, Topic2Post_idArticle) 
		VALUES 
		(aTopicId, aPostId);
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to insert or update a forum post.
--
-- If aPostId is 0 then insert a new post.
-- else update the post.
-- Save 'draft' or 'publish' the post depending on aAction.
-- A post must be published once before it can be viewed. 
--
DROP PROCEDURE IF EXISTS {$db->_['PInsertOrUpdatePost']};
CREATE PROCEDURE {$db->_['PInsertOrUpdatePost']}
(
	INOUT aPostId INT,
	INOUT aTopicId INT,
	OUT isPublished INT,
	OUT hasDraft INT,
	IN aUserId INT, 
	IN aTitle VARCHAR(256), 
	IN aContent BLOB,
	IN aAction CHAR(7) -- 'draft' or 'publish'
)
BEGIN
	DECLARE isPostPublished BOOLEAN;
	
	--
	-- First see if this is a completely new post, if it is, start by creating an empty post
	--
	IF aPostId = 0 THEN
	BEGIN
		INSERT INTO {$db->_['Article']}	(Article_idUser, createdArticle) VALUES (aUserId, NOW());
		SET aPostId = LAST_INSERT_ID();
	END;
	END IF;

	--
	-- Are we just saving a draft?
	--
	IF aAction = 'draft' THEN
	BEGIN
		UPDATE {$db->_['Article']} SET
			draftTitleArticle 		= aTitle,
			draftContentArticle 	= aContent,
			draftModifiedArticle	= NOW()
		WHERE
			idArticle = aPostId  AND
			{$db->_['FCheckUserIsOwnerOrAdmin']}(aPostId, aUserId)
		LIMIT 1;
	END;

	--
	-- Or are we publishing the post? Then prepare it and remove the draft.
	--
	ELSEIF aAction = 'publish' THEN
	BEGIN
		--
		-- Before we proceed, lets see if this post is published or not. 
		--
		SELECT publishedArticle INTO isPostPublished FROM {$db->_['Article']} WHERE idArticle = aPostId;

		--
		-- Need to do some extra work if this is the first time the post is published
		--
		IF isPostPublished IS NULL THEN
		BEGIN
			CALL {$db->_['PInitialPostPublish']}(aTopicId, aPostId, aUserId);
		END;
		END IF;
		
		--
		-- Re-publish the post it and remove the draft.
		--
		UPDATE {$db->_['Article']} SET
			titleArticle 					= aTitle,
			contentArticle 				= aContent,
			modifiedArticle				= NOW(),
			publishedArticle			= NOW(),
			draftTitleArticle 		= NULL,
			draftContentArticle 	= NULL,
			draftModifiedArticle	= NULL
		WHERE
			idArticle = aPostId  AND
			{$db->_['FCheckUserIsOwnerOrAdmin']}(aPostId, aUserId)
		LIMIT 1;	

	END;
	END IF;

	--
	-- Check some status issues, return as OUT parameters, might be useful in the GUI.
	--
	SELECT 
		IF(publishedArticle IS NULL, 0, 1),
		IF(draftModifiedArticle IS NULL, 0, 1)
		INTO 
		isPublished,
		hasDraft
	FROM {$db->_['Article']} 
	WHERE 
		idArticle = aPostId 
	;

END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Insert some default topics
--
SET @action='publish';
SET @post=0;
SET @topic=0;
CALL {$db->_['PInsertOrUpdatePost']} (@post, @topic, @notUsed, @notUsed, 1, 'Rome was not built in one day', 'At least, that is the common opinion.', @action);

SET @post=0;
CALL {$db->_['PInsertOrUpdatePost']} (@post,  @topic, @notUsed, @notUsed, 2, '', 'But you never now. I have heard otherwise.', @action);

SET @post=0;
SET @topic=0;
CALL {$db->_['PInsertOrUpdatePost']} (@post,  @topic, @notUsed, @notUsed, 2, 'A forum should be open for all', 'Everybody should be able to say what they feel.', @action);

SET @post=0;
CALL {$db->_['PInsertOrUpdatePost']} (@post,  @topic, @notUsed, @notUsed, 1, '', 'Is this really your opinion!!?', @action);

SET @post=0;
CALL {$db->_['PInsertOrUpdatePost']} (@post,  @topic, @notUsed, @notUsed, 2, '', 'No, just said it for the fun of it.', @action);

SET @post=0;
SET @topic=0;
CALL {$db->_['PInsertOrUpdatePost']} (@post,  @topic, @notUsed, @notUsed, 1, 'Which is the best forum ever?', 'I really would like to know your opinion on this matter.', @action);


EOD;


?>