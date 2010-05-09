<?php
// ===========================================================================================
//
// File: config.php
//
// Description: Config-file for database and SQL related issues. All SQL-statements are usually 
// stored in this directory (TP_SQLPATH). 
// This files contains global definitions for table names and so.
//
// Author: Mikael Roos, mos@bth.se
//


// -------------------------------------------------------------------------------------------
//
// Define the names for the database (tables, views, procedures, functions, triggers)
// Group per module. 
//
// Later on this part may be objekt to some rearrangement regarding 
// where the information is stored. But for now, make all database names public and available 
// in this single config-file.
// 

// -------------------------------------------------------------------------------------------
//
// Evaluating new way of configuring the names for tables, procedures, functions, triggers.
// Defining all in an array and making them accessable through the CDatabaseController.
//
// If it works properly will all defines be replaced by this array instead.
//
$DB_Tables_And_Procedures = Array(

	// Module Core: Accounts, users, groups
	'User'					 						=> DB_PREFIX . 'User',
	'PCreateAccount' 						=> DB_PREFIX . 'PCreateAccount',
	'PAuthenticateAccount' 			=> DB_PREFIX . 'PAuthenticateAccount',
	'PGetAccountDetails'				=> DB_PREFIX . 'PGetAccountDetails',
	'PChangeAccountPassword' 		=> DB_PREFIX . 'PChangeAccountPassword',
	'PChangeAccountEmail' 			=> DB_PREFIX . 'PChangeAccountEmail',
	'PChangeAccountAvatar' 			=> DB_PREFIX . 'PChangeAccountAvatar',
	'FCreatePassword' 					=> DB_PREFIX . 'FCreatePassword',
	'PGetMailAdressFromAccount' => DB_PREFIX . 'PGetMailAdressFromAccount',
	'PPasswordResetGetKey' 			=> DB_PREFIX . 'PPasswordResetGetKey',
	'PPasswordResetActivate' 		=> DB_PREFIX . 'PPasswordResetActivate',
	'PGetOrCreateAccountId' 		=> DB_PREFIX . 'PGetOrCreateAccountId',

	// For supporting gravatar from gratavar.com
	'PChangeAccountGravatar' 		=> DB_PREFIX . 'PChangeAccountGravatar',
	'FGetGravatarLinkFromEmail' => DB_PREFIX . 'FGetGravatarLinkFromEmail',


);



// -------------------------------------------------------------------------------------------
//
// Module Core
//
define('DBT_User', 				DB_PREFIX . 'User');
define('DBT_Group', 			DB_PREFIX . 'Group');
define('DBT_GroupMember',	DB_PREFIX . 'GroupMember');
define('DBT_Statistics',	DB_PREFIX . 'Statistics');

// Stored procedures

// User Defined Functions UDF
define('DBUDF_FCheckUserIsOwnerOrAdmin',	DB_PREFIX . 'FCheckUserIsOwnerOrAdmin');

// Triggers
define('DBTR_TInsertUser',		DB_PREFIX . 'TInsertUser');


// -------------------------------------------------------------------------------------------
//
// Module Forum_Romanum
// Some of these should eventually move to the core.
// Forum_Romanum should rely on the core.
//
// Tables
define('DBT_Article',			DB_PREFIX . 'Article');
define('DBT_Topic',				DB_PREFIX . 'Topic');
define('DBT_Topic2Post',	DB_PREFIX . 'Topic2Post');

// Stored procedures
define('DBSP_PGetArticleDetailsAndArticleList',	DB_PREFIX . 'PGetArticleDetailsAndArticleList');
define('DBSP_PGetArticleList',									DB_PREFIX . 'PGetArticleList');
define('DBSP_PGetArticleDetails',								DB_PREFIX . 'PGetArticleDetails');
define('DBSP_PInsertOrUpdateArticle',						DB_PREFIX . 'PInsertOrUpdateArticle');
define('DBSP_PGetTopicList',										DB_PREFIX . 'PGetTopicList');
define('DBSP_PGetTopicDetails',									DB_PREFIX . 'PGetTopicDetails');
define('DBSP_PGetTopicDetailsAndPosts',					DB_PREFIX . 'PGetTopicDetailsAndPosts');
define('DBSP_PGetPostDetails',									DB_PREFIX . 'PGetPostDetails');
define('DBSP_PInsertOrUpdatePost',							DB_PREFIX . 'PInsertOrUpdatepost');
define('DBSP_PInitialPostPublish',							DB_PREFIX . 'PInitialPostPublish');

// Triggers
define('DBTR_TAddArticle',		DB_PREFIX . 'TAddArticle');


?>