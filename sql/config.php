<?php
// ===========================================================================================
//
// config.php
//
// Config-file for database and SQL related issues. All SQL-statements are usually stored in this
// directory (TP_SQLPATH). This files contains global definitions for table names and so.
//
// Author: Mikael Roos, mos@bth.se
//


// -------------------------------------------------------------------------------------------
//
// Settings for the database connection
//
define('DB_HOST', 		'localhost');	// The database host
define('DB_USER', 		'Mikael');		// The username of the database
define('DB_PASSWORD', 	'hemligt');		// The users password
define('DB_DATABASE', 	'persia');		// The name of the database to use
define('DB_PREFIX', 	'pe_');		    // Prefix to use infront of tablename and views


// -------------------------------------------------------------------------------------------
//
// Define the names for the database (tables, views, procedures, functions, triggers)
//
define('DBT_User', 			DB_PREFIX . 'User');
define('DBT_Group', 		DB_PREFIX . 'Group');
define('DBT_GroupMember',	DB_PREFIX . 'GroupMember');
define('DBT_Statistics',	DB_PREFIX . 'Statistics');
define('DBT_Article',		DB_PREFIX . 'Article');
define('DBT_Topic',			DB_PREFIX . 'Topic');

// Stored procedures
define('DBSP_PGetArticleDetailsAndArticleList',	DB_PREFIX . 'PGetArticleDetailsAndArticleList');
define('DBSP_PGetArticleList',					DB_PREFIX . 'PGetArticleList');
define('DBSP_PGetArticleDetails',				DB_PREFIX . 'PGetArticleDetails');
define('DBSP_PInsertOrUpdateArticle',			DB_PREFIX . 'PInsertOrUpdateArticle');
define('DBSP_PGetTopicList',					DB_PREFIX . 'PGetTopicList');
define('DBSP_PGetTopicDetails',					DB_PREFIX . 'PGetTopicDetails');
define('DBSP_PGetPostDetails',					DB_PREFIX . 'PGetPostDetails');

// User Defined Functions UDF
define('DBUDF_FCheckUserIsOwnerOrAdmin',	DB_PREFIX . 'FCheckUserIsOwnerOrAdmin');

// Triggers
define('DBTR_TInsertUser',		DB_PREFIX . 'TInsertUser');
define('DBTR_TAddArticle',		DB_PREFIX . 'TAddArticle');




?>