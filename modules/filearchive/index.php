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
	
	//
	// File Archive
	//
	case 'home':					require_once($currentDir . 'PIndex.php'); break;
	case 'archive':				require_once($currentDir . 'PFileArchive.php'); break;
	case 'file-details':	require_once($currentDir . 'PFileDetails.php'); break;
	case 'file-detailsp':	require_once($currentDir . 'PFileDetailsProcess.php'); break;
	case 'upload':				require_once($currentDir . 'PFileUpload.php'); break;
	case 'uploadp':				require_once($currentDir . 'PFileUploadProcess.php'); break;


	//
	// Login, logout
	//
	case 'login':			require_once(TP_PAGESPATH . 'login/PLogin.php'); break;
	case 'loginp':		require_once(TP_PAGESPATH . 'login/PLoginProcess.php'); break;
	case 'logoutp':		require_once(TP_PAGESPATH . 'login/PLogoutProcess.php'); break;

	//
	// Install database
	//
	case 'install':		require_once(TP_PAGESPATH . 'install/PInstall.php'); break;
	case 'installp':	require_once(TP_PAGESPATH . 'install/PInstallProcess.php'); break;
	
	//
	// User, profile and settings
	//
	
	// Create new account
	case 'account-create': 	if(CREATE_NEW_ACCOUNT) require_once(TP_PAGESPATH . 'account/PAccountCreate.php'); break;
	case 'account-createp':	if(CREATE_NEW_ACCOUNT) require_once(TP_PAGESPATH . 'account/PAccountCreateProcess.php'); break;

	// Maintain account profile
	case 'account-settings':			require_once(TP_PAGESPATH . 'account/PAccountSettings.php'); break;
	case 'account-update':				require_once(TP_PAGESPATH . 'account/PAccountSettingsProcess.php'); break;

	// Process for aid with resetting password
	case 'account-forgot-pwd':		if(FORGOT_PASSWORD) require_once(TP_PAGESPATH . 'account/PAccountForgotPassword1.php'); break;
	case 'account-forgot-pwdp':		if(FORGOT_PASSWORD) require_once(TP_PAGESPATH . 'account/PAccountForgotPassword1Process.php'); break;
	case 'account-forgot-pwd2':		if(FORGOT_PASSWORD) require_once(TP_PAGESPATH . 'account/PAccountForgotPassword2.php'); break;
	case 'account-forgot-pwd2p':	if(FORGOT_PASSWORD) require_once(TP_PAGESPATH . 'account/PAccountForgotPassword2Process.php'); break;
	case 'account-forgot-pwd3':		if(FORGOT_PASSWORD) require_once(TP_PAGESPATH . 'account/PAccountForgotPassword3.php'); break;
	case 'account-forgot-pwd3p':	if(FORGOT_PASSWORD) require_once(TP_PAGESPATH . 'account/PAccountForgotPassword3Process.php'); break;
	case 'account-forgot-pwd4':		if(FORGOT_PASSWORD) require_once(TP_PAGESPATH . 'account/PAccountForgotPassword4.php'); break;

	//
	// Using common files from modules/core
	//
	case 'ls':	require_once(TP_MODULESPATH . '/core/viewfiles/PListDirectory.php'); break;

	//
	// Trying to access a forbidden page, or having no permissions.
	//
	case 'p403':	require_once(TP_MODULESPATH . '/core/home/P403.php'); break;

	// Default case, trying to access some unknown page, should present some error message
	// or show the home-page
	//
	case 'p404':
	default:		require_once(TP_MODULESPATH . '/core/home/P404.php'); break;
}


?>