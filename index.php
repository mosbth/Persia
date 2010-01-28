<?php
// ===========================================================================================
//
// index.php
//
// An implementation of a PHP frontcontroller for a web-site.
//
// All requests passes through this page, for each request a pagecontroller is choosen.
// The pagecontroller results in a response or a redirect.
//
// Author: Mikael Roos, mos@bth.se
//

// -------------------------------------------------------------------------------------------
//
// Require the files that are common for all pagecontrollers.
//
session_start();
require_once('config.php');

//
// start a timer to time the generation of this page (excluding config.php)
//
if(WS_TIMER) {
	$gTimerStart = microtime(TRUE);
}

//
// Enable autoload for classes
//
function __autoload($class_name) {
    require_once(TP_SOURCEPATH . $class_name . '.php');
}


// -------------------------------------------------------------------------------------------
//
// Redirect to the choosen pagecontroller.
//
$gPage = isset($_GET['p']) ? $_GET['p'] : 'home';

switch($gPage) {

	//
	// The home-page
	//
	case 'home':		require_once(TP_PAGESPATH . 'home/PIndex.php'); break;
	case 'about':		require_once(TP_PAGESPATH . 'home/PAbout.php'); break;
	case 'template':	require_once(TP_PAGESPATH . 'home/PTemplate.php'); break;
	
	//
	// Install database
	//
	case 'install':		require_once(TP_PAGESPATH . 'install/PInstall.php'); break;
	case 'installp':	require_once(TP_PAGESPATH . 'install/PInstallProcess.php'); break;
	
	//
	// Login, logout
	//
	case 'login':		require_once(TP_PAGESPATH . 'login/PLogin.php'); break;
	case 'loginp':		require_once(TP_PAGESPATH . 'login/PLoginProcess.php'); break;
	case 'logoutp':		require_once(TP_PAGESPATH . 'login/PLogoutProcess.php'); break;

	//
	// Administration
	//
	case 'admin':		require_once(TP_PAGESPATH . 'admin_users/PUsersList.php'); break;

	//
	// User Profile
	//
	case 'account-details':		require_once(TP_PAGESPATH . 'account/PAccount.php'); break;
	case 'style-details':		require_once(TP_PAGESPATH . 'style/PStyle.php'); break;
	//
	case 'settingsprofile':		require_once(TP_PAGESPATH . 'userprofile/PProfileShow.php'); break;
	case 'account':				require_once(TP_PAGESPATH . 'account/PAccount.php'); break;

	//
	// Directory listning
	//
	case 'ls':	require_once(TP_PAGESPATH . 'viewfiles/PListDirectory.php'); break;
	
	//
	// Forum
	//
	case 'article-edit':		require_once(TP_PAGESPATH . 'forum/PArticleEdit.php'); break;
	case 'article-save':		require_once(TP_PAGESPATH . 'forum/PArticleSave.php'); break;
	case 'article-delete':		require_once(TP_PAGESPATH . 'forum/PArticleDelete.php'); break;
	case 'article-show':		require_once(TP_PAGESPATH . 'forum/PArticleShow.php'); break;

	//
	// Style Your Web, app_syw
	// Example for working with stylesheets
	//
	/*
	case 'install':		require_once(TP_PAGESPATH . 'app_syw/install/PInstall.php'); break;
	case 'installp':	require_once(TP_PAGESPATH . 'app_syw/install/PInstallProcess.php'); break;
	case 'home':		require_once(TP_PAGESPATH . 'app_syw/PIndex.php'); break;
	case 'style':		require_once(TP_PAGESPATH . 'app_syw/PShowStyle.php'); break;
	case 'minwidth':	require_once(TP_PAGESPATH . 'app_syw/PMinWidth.php'); break;
	case 'centered':	require_once(TP_PAGESPATH . 'app_syw/PCentered.php'); break;
	case '2cols':		require_once(TP_PAGESPATH . 'app_syw/P2Columns.php'); break;
	case '3cols':		require_once(TP_PAGESPATH . 'app_syw/P3Columns.php'); break;
	case '123cols':		require_once(TP_PAGESPATH . 'app_syw/P123Columns.php'); break;
	case 'liquid':		require_once(TP_PAGESPATH . 'app_syw/P123Liquid.php'); break;
	*/

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
	//
	// Blog
	//
	case 'install':		require_once(TP_PAGESPATH . 'blog/install/PInstall.php'); break;
	case 'installp':	require_once(TP_PAGESPATH . 'blog/install/PInstallProcess.php'); break;
	case 'home':		require_once(TP_PAGESPATH . 'blog/PHome.php'); break;
	case 'post':		require_once(TP_PAGESPATH . 'blog/PPost.php'); break;
	case 'poste':		require_once(TP_PAGESPATH . 'blog/PPostEdit.php'); break;
	*/
	
	//
	// Default case, trying to access some unknown page, should present some error message
	// or show the home-page
	//
	default:			require_once(TP_PAGESPATH . 'home/P404.php'); break;
}


?>