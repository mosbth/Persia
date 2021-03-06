<?php
// ===========================================================================================
//
// File: CPagecontroller.php
//
// Description: Nice to have utility for common methods useful in most pagecontrollers.
//
// Author: Mikael Roos, mos@bth.se
//

class CPageController {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	protected static $iInstance = NULL;
	public $lang = Array();
	

	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	protected function __construct() {
		$_SESSION['history3'] = CPageController::SESSIONisSetOrSetDefault('history2');
		$_SESSION['history2'] = CPageController::SESSIONisSetOrSetDefault('history1');
		$_SESSION['history1'] = CPageController::CurrentURL();
		//print_r($_SESSION);
	}


	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() { ; }


	// ------------------------------------------------------------------------------------
	//
	// Singleton, get the instance or create a new one.
	//
	public static function GetInstance() {
		
		if(self::$iInstance == NULL) {
			self::$iInstance = new CPageController();
		}
		return self::$iInstance;
	}


	// ------------------------------------------------------------------------------------
	//
	// For usability, get instance and load languagefile.
	//
	public static function GetInstanceAndLoadLanguage($aFilename) {
		
		self::GetInstance();
		self::$iInstance->LoadLanguage($aFilename);
		return self::$iInstance;
	}


	// ------------------------------------------------------------------------------------
	//
	// Load language file
	//
	public function LoadLanguage($aFilename) {

		// Load language file, all language files in the TP_LANGUAGEPATH
		//$langFile = TP_LANGUAGEPATH . WS_LANGUAGE . '/' . substr($aFilename, strlen(TP_ROOT));

		// All language files in the a lang-subdirectory from the original file.
		$file = basename($aFilename);
		$dir  = dirname($aFilename);
		$langFile = $dir . '/lang/' . WS_LANGUAGE . '/' . $file;

		if(!file_exists($langFile)) {
			die(sprintf("Language file does not exists: %s", $langFile));
		}

		require_once($langFile);
		$this->lang = array_merge($this->lang, $lang);
	}


	// ------------------------------------------------------------------------------------
	//
	// Set message in session, get and clear the message by using GetSessionMessage
	//
	public static function SetSessionMessage($aVar, $aMessage) {
		$message = isset($_SESSION[$aVar]) ? $_SESSION[$aVar] : '';
		$_SESSION[$aVar] = $message . $aMessage;
	}


	// ------------------------------------------------------------------------------------
	//
	// Get (and Clear) message in session, set the message by using SetSessionMessage
	//
	// This method should be rewritten to not unset the message, but then all occurences of
	// it should be found in the code and replaced with GetAndClearSessionMessage.
	//
	public static function GetSessionMessage($aVar) {
		return self::GetAndClearSessionMessage($aVar);
	}

	// ------------------------------------------------------------------------------------
	//
	// Get and Clear message in session, set the message by using SetSessionMessage
	//
	public static function GetAndClearSessionMessage($aVar, $aDefaultValue="") {

		$message = "";
		if(isset($_SESSION[$aVar])) {    
			$message = $_SESSION[$aVar];
			unset($_SESSION[$aVar]);
		} else {
			$message = $aDefaultValue;
		}
		return $message;
	}


	// ------------------------------------------------------------------------------------
	//
	// Check if corresponding $_GET[''] is set, then use it or return the default value.
	//
	public static function GETisSetOrSetDefault($aEntry, $aDefault = '') {

		return isset($_GET["$aEntry"]) && !empty($_GET["$aEntry"]) ? $_GET["$aEntry"] : $aDefault;
	}


	// ------------------------------------------------------------------------------------
	//
	// Check if corresponding $_POST[''] is set, then use it or return the default value.
	//
	public static function POSTisSetOrSetDefault($aEntry, $aDefault = '') {

		return isset($_POST["$aEntry"]) && !empty($_POST["$aEntry"]) ? $_POST["$aEntry"] : $aDefault;
	}


	// ------------------------------------------------------------------------------------
	//
	// Check if corresponding $_SESSION[''] is set, then use it or return the default value.
	//
	public static function SESSIONisSetOrSetDefault($aEntry, $aDefault = '') {

		return isset($_SESSION["$aEntry"]) && !empty($_SESSION["$aEntry"]) ? $_SESSION["$aEntry"] : $aDefault;
	}


	// ------------------------------------------------------------------------------------
	//
	// Check the $_POST first and then the $_GET for a value. 
	//
	public static function POSTorGETisSetOrSetDefault($aEntry, $aDefault = '') {

		$post = self::POSTisSetOrSetDefault($aEntry, '');		
		$get 	= self::GETisSetOrSetDefault($aEntry, '');
		return (!empty($post) ? $post : (!empty($get) ? $get : $aDefault));
	}


	// ------------------------------------------------------------------------------------
	//
	// Check the $_POST first and then the $_SESSION for a value. 
	// Unset the value in the $_SESSION.
	//
	public static function POSTorSESSIONisSetOrSetDefaultClearSESSION($aEntry, $aDefault = '') {

		$post 		= self::POSTisSetOrSetDefault($aEntry, '');		
		$session 	= self::SESSIONisSetOrSetDefault($aEntry, '');
		unset($_SESSION["$aEntry"]);
		return (!empty($post) ? $post : (!empty($session) ? $session : $aDefault));
	}


	// ------------------------------------------------------------------------------------
	//
	// Check if the value is numeric and optional in the range.
	//
	public static function IsNumericOrDie($aVar, $aRangeLow = 0, $aRangeHigh = 0) {

		$inRangeH = empty($aRangeHigh) ? TRUE : ($aVar <= $aRangeHigh);
		$inRangeL = empty($aRangeLow)  ? TRUE : ($aVar >= $aRangeLow);
		if(!(is_numeric($aVar) && $inRangeH && $inRangeL)) {
			die(sprintf("The variable value '$s' is not numeric or it is out of range.", $aVar));
		}
		return $aVar;
	}


	// ------------------------------------------------------------------------------------
	//
	// Check if the value is a string.
	//
	public static function IsStringOrDie($aVar) {

		if(!is_string($aVar)) {
			die(sprintf("The variable value '$s' is not a string.", $aVar));
		}
	}


	// ------------------------------------------------------------------------------------
	//
	// Static function
	// Redirect to another page
	// Support $aUri to be local uri within site or external site (starting with http://)
	// If empty, redirect to home page of current module.
	//
	public static function RedirectTo($aUri) {

		if(empty($aUri)) {
			CPageController::RedirectToModuleAndPage();			
		} else if(!strncmp($aUri, "http://", 7)) {
			;
		} else if(!strncmp($aUri, "?", 1)) {
			$aUri = WS_SITELINK . "{$aUri}";
		} else {
			$aUri = WS_SITELINK . "?p={$aUri}";
		}

		header("Location: {$aUri}");
		exit;
	}


	// ------------------------------------------------------------------------------------
	//
	// Static function
	// Redirect to another local page using module, page and arguments (Array)
	// Defaults to current module home-page.
	//
	public static function UrlToModuleAndPage($aModule='', $aPage='home') {

		global $gModule;
		
		$m = (empty($aModule)) ? "m={$gModule}" : "m={$aModule}";
		$p = "p={$aPage}";
		$aUrl = WS_SITELINK . "?{$m}&{$p}";

		// Enable sending $aArguments as an Array later on. When needed.
		
		// Set message in SESSION, if defined, When needed.

		return $aUrl;
	}


	// ------------------------------------------------------------------------------------
	//
	// Static function
	// Redirect to another local page using module, page and arguments (Array)
	// Defaults to current module home-page.
	//
	public static function RedirectToModuleAndPage($aModule='', $aPage='home', $aArguments='', $aMessage='') {

		global $gModule;
		
		$m = (empty($aModule)) ? "m={$gModule}" : "m={$aModule}";
		$p = "p={$aPage}";
		$aUrl = WS_SITELINK . "?{$m}&{$p}";

		// Enable sending $aArguments as an Array later on. When needed.
		
		
		// Set message in SESSION, if defined
		if(!empty($aMessage)) {
			self::SetSessionMessage($aPage, $aMessage);
		}

		header("Location: {$aUrl}");
		exit;
	}


	// ------------------------------------------------------------------------------------
	//
	// Static function
	// Create a URL to the current page.
	//
	public static function CurrentURL() {

		// Create link to current page
		$refToThisPage = "http";
		$refToThisPage .= (@$_SERVER["HTTPS"] == "on") ? 's' : '';
		$refToThisPage .= "://";
		$serverPort = ($_SERVER["SERVER_PORT"] == "80") ? '' : 
										(($_SERVER["SERVER_PORT"] == 443 && @$_SERVER["HTTPS"] == "on") ? '' 
											: ":{$_SERVER['SERVER_PORT']}"
										);
		$refToThisPage .= $_SERVER["SERVER_NAME"] . $serverPort . $_SERVER["REQUEST_URI"];
		
		return $refToThisPage;
	}


	// ------------------------------------------------------------------------------------
	//
	// Static function
	// Parse current URL into an array. Return the array.
	// 
	public static function ParseCurrentURL() {

		$current 	= self::CurrentURL();
		$url 			= parse_url($current);
		parse_str($url['query'], $url['query']);
		
		return $url;
	}


	// ------------------------------------------------------------------------------------
	//
	// Static function
	// Parse current URL and modify its querystring. Return the modified url.
	// 
	public static function ModifyCurrentURL($aQueryStr) {
		
		// Build current url
		$url	 = self::ParseCurrentURL();
		$query = Array();
		parse_str($aQueryStr, $query);
		
		// Modify the querystring
		$url['query'] = array_merge($url['query'], $query);
		
		// Rebuild the url from the array
		$newQuery = http_build_query($url['query']);
		$newQuery = (empty($newQuery) ? '' : "?{$newQuery}");
		$password = (empty($url['password']) 	? '' : ":{$url['password']}");
		$userPwd	= (empty($url['name']) 			? '' : "{$url['name']}{$password}@");
		$fragment = (empty($url['fragment']) 	? '' : "#{$url['fragment']}");
		$urlAsString = "{$url['scheme']}://{$userPwd}{$url['host']}{$url['path']}{$newQuery}{$fragment}";
									
		return $urlAsString;
	}


	// ------------------------------------------------------------------------------------
	//
	// Static function
	// Add trailing slash if missing. Return the modified url.
	// 
	public static function AddTrailingSlashIfNeeded($aUrl) {
		return (!$aUrl[strlen($aUrl)] == '/') ? $aUrl . '/' : $aUrl;
	}


	// ------------------------------------------------------------------------------------
	//
	// Static function
	// Add trailing DIRECTORY_SEPARATOR if missing. Return the modified path.
	// 
	public static function AddTrailingSeparatorIfNeeded($aPath) {
		$l = strlen(DIRECTORY_SEPARATOR);
		return $aPath . (substr_compare($aPath, DIRECTORY_SEPARATOR, strlen($aPath)-$l, $l) == 0 ? '' : DIRECTORY_SEPARATOR);
	}


	// ------------------------------------------------------------------------------------
	//
	// OBSOLETE. Replaced by SetSessionMessage.
	//
	//
	// Set global error message/notice, used and cleared by CHTMLPage
	//
	public static function SetSessionErrorMessage($aMessage) {
		self::SetSessionMessage('errorMessage', $aMessage);
	}


	// ------------------------------------------------------------------------------------
	//
	// OBSOLETE. Replaced by SetSessionMessage.
	//
	// Set global success message/notice, used and cleared by CHTMLPage
	//
	public static function SetSessionSuccessMessage($aMessage) {
		self::SetSessionMessage('successMessage', $aMessage);
	}



} // End of Of Class

?>