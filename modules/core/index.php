<?php
// ===========================================================================================
//
// index.php
//
// Modulecontroller. An implementation of a PHP module frontcontroller (module controller). 
// This page is called from the global frontcontroller. Its function could be named a 
// sub-frontcontroller or module frontcontroller. I call it a modulecontroller.
//
// All requests passes through this page, for each request a pagecontroller is choosen.
// The pagecontroller results in a response or a redirect.
//
// Author: Mikael Roos, mos@bth.se
//


// -------------------------------------------------------------------------------------------
//
// Redirect to the choosen pagecontroller.
//
$currentDir = dirname(__FILE__) . '/';
global $gPage;

switch($gPage) {

	// -------------------------------------------------------------------------------------------
	//
	// The home-page
	//
	case 'home':			require_once(TP_PAGESPATH . 'home/PIndex.php'); break;
	case 'about':			require_once(TP_PAGESPATH . 'home/PAbout.php'); break;
	case 'credits':		require_once(TP_PAGESPATH . 'home/PAbout.php'); break;
	case 'legal':			require_once(TP_PAGESPATH . 'home/PAbout.php'); break;
	case 'privacy':		require_once(TP_PAGESPATH . 'home/PAbout.php'); break;
	case 'template':	require_once(TP_PAGESPATH . 'home/PTemplate.php'); break;
	case 'empty':			require_once(TP_PAGESPATH . 'home/PEmpty.php'); break;
	
	
	// -------------------------------------------------------------------------------------------
	//
	// Install database
	//
	case 'install':		require_once(TP_PAGESPATH . 'install/PInstall.php'); break;
	case 'installp':	require_once(TP_PAGESPATH . 'install/PInstallProcess.php'); break;
	
	
	// -------------------------------------------------------------------------------------------
	//
	// Login, logout
	//
	case 'login':		require_once(TP_PAGESPATH . 'login/PLogin.php'); break;
	case 'loginp':	require_once(TP_PAGESPATH . 'login/PLoginProcess.php'); break;
	case 'logoutp':	require_once(TP_PAGESPATH . 'login/PLogoutProcess.php'); break;


	// -------------------------------------------------------------------------------------------
	//
	// Create new account and reset password
	//
	
	// Create new account
	case 'account-create': 	if(CREATE_NEW_ACCOUNT) require_once(TP_PAGESPATH . 'account/PAccountCreate.php'); break;
	case 'account-createp':	if(CREATE_NEW_ACCOUNT) require_once(TP_PAGESPATH . 'account/PAccountCreateProcess.php'); break;

	// Process for aid with resetting password
	case 'account-forgot-pwd':		if(FORGOT_PASSWORD) require_once(TP_PAGESPATH . 'account/PAccountForgotPassword1.php'); break;
	case 'account-forgot-pwdp':		if(FORGOT_PASSWORD) require_once(TP_PAGESPATH . 'account/PAccountForgotPasswordProcess.php'); break;
	case 'account-forgot-pwd2':		if(FORGOT_PASSWORD) require_once(TP_PAGESPATH . 'account/PAccountForgotPassword2.php'); break;
	case 'account-forgot-pwd2p':	if(FORGOT_PASSWORD) require_once(TP_PAGESPATH . 'account/PAccountForgotPasswordProcess.php'); break;
	case 'account-forgot-pwd3':		if(FORGOT_PASSWORD) require_once(TP_PAGESPATH . 'account/PAccountForgotPassword3.php'); break;
	case 'account-forgot-pwd3p':	if(FORGOT_PASSWORD) require_once(TP_PAGESPATH . 'account/PAccountForgotPasswordProcess.php'); break;
	case 'account-forgot-pwd4':		if(FORGOT_PASSWORD) require_once(TP_PAGESPATH . 'account/PAccountForgotPassword4.php'); break;


	// -------------------------------------------------------------------------------------------
	//
	// User Control Panel (UCP), default
	//
	case 'ucp':			require_once(TP_PAGESPATH . 'ucp/PUserControlPanel.php'); break;

	// User Control Panel (UCP), Maintain account profile
	case 'ucp-account-settings':			require_once(TP_PAGESPATH . 'ucp/PAccountSettings.php'); break;
	case 'ucp-account-update':				require_once(TP_PAGESPATH . 'ucp/PAccountSettingsProcess.php'); break;

	// User Control Panel (UCP), Filearchive
	case 'ucp-filearchive':		require_once(TP_PAGESPATH . 'ucp/PFileArchive.php'); break;
	case 'ucp-fileupload':		require_once(TP_PAGESPATH . 'ucp/PFileUpload.php'); break;
	case 'ucp-fileuploadp':		require_once(TP_PAGESPATH . 'ucp/PFileUploadProcess.php'); break;
	case 'ucp-filedetails':		require_once(TP_PAGESPATH . 'ucp/PFileDetails.php'); break;
	case 'ucp-filedetailsp':	require_once(TP_PAGESPATH . 'ucp/PFileDetailsProcess.php'); break;

	// File download
	case 'download':		require_once(TP_PAGESPATH . 'file/PFileDownload.php'); break;
	case 'download-now':		require_once(TP_PAGESPATH . 'file/PFileDownloadProcess.php'); break;


	// -------------------------------------------------------------------------------------------
	//
	// Admin Control Panel (UCP), default
	//
	case 'acp':			require_once(TP_PAGESPATH . 'acp/PAdminControlPanel.php'); break;

	// Accounts
	case 'acp-accounts':			require_once(TP_PAGESPATH . 'acp/PAccountList.php'); break;

	// Groups
	case 'acp-groups':				require_once(TP_PAGESPATH . 'acp/PGroupList.php'); break;
	case 'acp-groupdetails':	require_once(TP_PAGESPATH . 'acp/PGroupDetails.php'); break;
	case 'acp-groupmembers':	require_once(TP_PAGESPATH . 'acp/PGroupDetails.php'); break;
	case 'acp-groupdetailsp':	require_once(TP_PAGESPATH . 'acp/PGroupDetailsProcess.php'); break;
	case 'acp-groupcreatep':	require_once(TP_PAGESPATH . 'acp/PGroupDetailsProcess.php'); break;
	case 'acp-groupdelp':			require_once(TP_PAGESPATH . 'acp/PGroupDetailsProcess.php'); break;
	case 'acp-groupmemremp':	require_once(TP_PAGESPATH . 'acp/PGroupDetailsProcess.php'); break;


	// -------------------------------------------------------------------------------------------
	//
	// Directory listning
	//
	case 'ls':	require_once(TP_PAGESPATH . 'viewfiles/PListDirectory.php'); break;

	
	// -------------------------------------------------------------------------------------------
	//
	// Article (Forum Romanum 0.1 (0.01))
	//
	//case 'article-edit':	require_once(TP_PAGESPATH . 'forum/PArticleEdit.php'); break;
	case 'article-save':		require_once(TP_PAGESPATH . 'forum/PArticleSave.php'); break;
	case 'article-delete':	require_once(TP_PAGESPATH . 'forum/PArticleDelete.php'); break;
	case 'article-show':		require_once(TP_PAGESPATH . 'forum/PArticleShow.php'); break;
	case 'topics':					require_once(TP_PAGESPATH . 'forum/PTopics.php'); break;
	case 'topic':						require_once(TP_PAGESPATH . 'forum/PTopicShow.php'); break;
	case 'post-edit':				require_once(TP_PAGESPATH . 'forum/PPostEdit.php'); break;

	// Testing JavaScript editors
	case 'article-edit':			require_once(TP_PAGESPATH . 'forum/jseditors/PArticleEdit.php'); break;
	case 'article-edit-all':	require_once(TP_PAGESPATH . 'forum/jseditors/PArticleEditAll.php'); break;
	case 'article-nicedit':		require_once(TP_PAGESPATH . 'forum/jseditors/PArticleEdit_NicEdit.php'); break;
	case 'article-wymeditor':	require_once(TP_PAGESPATH . 'forum/jseditors/PArticleEdit_WYMeditor.php'); break;
	case 'article-markitup':	require_once(TP_PAGESPATH . 'forum/jseditors/PArticleEdit_markItUp.php'); break;


	// -------------------------------------------------------------------------------------------
	//
	// Rate My Professor, app_rmp
	// Show, add, edit, delete professors
	//
	/*
	case 'home':			require_once(TP_PAGESPATH . 'app_rmp/PIndex.php'); break;
	case 'install':			require_once(TP_PAGESPATH . 'app_rmp/install/PInstall.php'); break;
	case 'installp':		require_once(TP_PAGESPATH . 'app_rmp/install/PInstallProcess.php'); break;
	case 'visalarare':		require_once(TP_PAGESPATH . 'app_rmp/PVisaLarare.php'); break;
	case 'insertlarare':	require_once(TP_PAGESPATH . 'app_rmp/PInsertLarare.php'); break;
	case 'deletelarare':	require_once(TP_PAGESPATH . 'app_rmp/PDeleteLarare.php'); break;
	case 'editlarare':		require_once(TP_PAGESPATH . 'app_rmp/PEditLarareInfo.php'); break;
	case 'editlararep':		require_once(TP_PAGESPATH . 'app_rmp/PEditLarareInfoProcess.php'); break;
	case 'visalararebetyg':	require_once(TP_PAGESPATH . 'app_rmp/PVisaLarareBetyg.php'); break;
	case 'kommentera':		require_once(TP_PAGESPATH . 'app_rmp/PSattBetygLarare.php'); break;
	case 'kommenterap':		require_once(TP_PAGESPATH . 'app_rmp/PSattBetygLarareProcess.php'); break;
	*/
	
	/*
	// -------------------------------------------------------------------------------------------
	//
	// Blog
	//
	case 'install':		require_once(TP_PAGESPATH . 'blog/install/PInstall.php'); break;
	case 'installp':	require_once(TP_PAGESPATH . 'blog/install/PInstallProcess.php'); break;
	case 'home':		require_once(TP_PAGESPATH . 'blog/PHome.php'); break;
	case 'post':		require_once(TP_PAGESPATH . 'blog/PPost.php'); break;
	case 'poste':		require_once(TP_PAGESPATH . 'blog/PPostEdit.php'); break;
	*/

	
	// -------------------------------------------------------------------------------------------
	//
	// Trying to access a forbidden page, or having no permissions.
	//
	case 'p403':	require_once(TP_MODULESPATH . '/core/home/P403.php'); break;


	// -------------------------------------------------------------------------------------------
	//
	// Default case, trying to access some unknown page, should present some error message
	// or show the home-page
	//
	case 'p404':
	default:			require_once(TP_PAGESPATH . 'home/P404.php'); break;
}

require_once(TP_PAGESPATH . 'home/P404.php')

?>