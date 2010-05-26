<?php
// ===========================================================================================
//
// File: config.php (Database config, except connection parameters)
//
// Description: 
// Define the names for the database (tables, views, procedures, functions, triggers)
// Defining all in an array and making them accessable through the CDatabaseController.
// Group per module. 
//
// Author: Mikael Roos, mos@bth.se
//
// History:
// 2010-05-11: Now organised according the new array-format, dropped the define()
//

//
// Enable language support for error messages
//
$pc = new CPageController();
$pc->LoadLanguage(__FILE__);


$DB_Tables_And_Procedures = Array(

	// -------------------------------------------------------------------------------------------
	//
	// Module Core
	//
	'DefaultCharacterSet'	=> 'utf8',
	'DefaultCollate'			=> 'utf8_unicode_ci',


	//
	// Module Core: Accounts, users, groups, profile
	//
	'User'					 						=> DB_PREFIX . 'User',
	'Group'					 						=> DB_PREFIX . 'Group',
	'GroupMember'								=> DB_PREFIX . 'GroupMember',
	'Statistics'					 			=> DB_PREFIX . 'Statistics',
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
	'TInsertUser' 							=> DB_PREFIX . 'TInsertUser',
	'FGetAvatar' 								=> DB_PREFIX . 'FGetAvatar',
	'FCheckUserIsAdmin' 				=> DB_PREFIX . 'FCheckUserIsAdmin',

	// For supporting gravatar from gratavar.com
	'PChangeAccountGravatar' 		=> DB_PREFIX . 'PChangeAccountGravatar',
	'FGetGravatarLinkFromEmail' => DB_PREFIX . 'FGetGravatarLinkFromEmail',

	//
	// Module Core: Article
	//
	'Article' 													=> DB_PREFIX . 'Article',
	'PInsertOrUpdateArticle' 						=> DB_PREFIX . 'PInsertOrUpdateArticle',
	'PGetArticleDetails' 								=> DB_PREFIX . 'PGetArticleDetails',
	'PGetArticleList' 									=> DB_PREFIX . 'PGetArticleList',
	'PGetArticleDetailsAndArticleList'	=> DB_PREFIX . 'PGetArticleDetailsAndArticleList',
	'FCheckUserIsOwnerOrAdmin' 					=> DB_PREFIX . 'FCheckUserIsOwnerOrAdmin',
	'TAddArticle' 											=> DB_PREFIX . 'TAddArticle',

	//
	// Module Core: File
	//
	'CSizeFileName' 					=> 256,
	'CSizeFileNameUnique' 		=> 13, // Smallest size of PHP uniq().
	'CSizePathToDisk' 				=> 256,
	
	 // Max 127 chars according http://tools.ietf.org/html/rfc4288#section-4.2
	'CSizeMimetype'		 				=> 127,
	
	'File' 										=> DB_PREFIX . 'File',
	'PInsertFile' 						=> DB_PREFIX . 'PInsertFile',
	'PFileDetails'						=> DB_PREFIX . 'PFileDetails',
	'PFileDetailsUpdate'			=> DB_PREFIX . 'PFileDetailsUpdate',
	'PListFiles' 							=> DB_PREFIX . 'PListFiles',
	'PFileDetailsDeleted' 		=> DB_PREFIX . 'PFileDetailsDeleted',

	// Check permissions and success values
	'FFileCheckPermission' 				=> DB_PREFIX . 'FFileCheckPermission',
	'FFileCheckPermissionMessages' => Array(
			'1' => $pc->lang['FILE_NO_PERMISSION'],
			'2' => $pc->lang['FILE_DOES_NOT_EXISTS'],
		),


	// -------------------------------------------------------------------------------------------
	//
	// Module Forum_Romanum
	//
	'Topic' 										=> DB_PREFIX . 'Topic',
	'Topic2Post' 								=> DB_PREFIX . 'Topic2Post',
	'PGetTopicList' 						=> DB_PREFIX . 'PGetTopicList',
	'PGetTopicDetails' 					=> DB_PREFIX . 'PGetTopicDetails',
	'PGetTopicDetailsAndPosts' 	=> DB_PREFIX . 'PGetTopicDetailsAndPosts',
	'PGetPostDetails' 					=> DB_PREFIX . 'PGetPostDetails',
	'PInitialPostPublish' 			=> DB_PREFIX . 'PInitialPostPublish',
	'PInsertOrUpdatePost' 			=> DB_PREFIX . 'PInsertOrUpdatePost',


); // End Of Array Creation


?>