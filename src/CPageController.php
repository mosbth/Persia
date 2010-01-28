<?php
// ===========================================================================================
//
// Class CPagecontroller
//
// Nice to have utility for common methods useful in most pagecontrollers.
//

class CPageController {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	public $lang = Array();
	

	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct() {
		;
	}


	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() {
		;
	}


	// ------------------------------------------------------------------------------------
	//
	// Load language file
	//
	public function LoadLanguage($aFilename) {

		// Load language file
		$langFile = TP_LANGUAGEPATH . WS_LANGUAGE . '/' . substr($aFilename, strlen(TP_ROOT));

		if(!file_exists($langFile)) {
			die(sprintf("Language file does not exists: $s", $langFile));
		}

		require_once($langFile);
		$this->lang = array_merge($this->lang, $lang);
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
	// Check if the value is numeric and optional in the range.
	//
	public static function IsNumericOrDie($aVar, $aRangeLow = '', $aRangeHigh = "") {

		$inRangeH = empty($aRangeHigh) ? TRUE : ($aVar <= $aRangeHigh);
		$inRangeL = empty($aRangeLow)  ? TRUE : ($aVar >= $aRangeLow);
		if(!(is_numeric($aVar) && $inRangeH && inRangeL)) {
			die(sprintf("The variable value '$s' is not numeric or it is out of range.", $aVar));
		}
	}


	// ------------------------------------------------------------------------------------
	//
	// Static function, HTML helper
	// Create a horisontal sidebar menu
	//
	public static function GetSidebarMenu($aMenuitems, $aTarget="") {

		global $gPage;

		$target = empty($aTarget) ? $gPage : $aTarget;

		$menu = "<ul>";
		foreach($aMenuitems as $key => $value) {
			$selected = (strcmp($target, substr($value, 3)) == 0) ? " class='sel'" : "";
			$menu .= "<li{$selected}><a href='{$value}'>{$key}</a></li>";
		}
		$menu .= '</ul>';
		
		return $menu;
	}


	// ------------------------------------------------------------------------------------
	//
	// Static function
	// Redirect to another page
	// Support $aUri to be local uri within site or external site (starting with http://)
	//
	public static function RedirectTo($aUri) {

		if(strncmp($aUri, "http://", 7)) {
			$aUri = WS_SITELINK . "?p={$aUri}";
		}

		header("Location: {$aUri}");
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
		$serverPort = ($_SERVER["SERVER_PORT"] == "80") ? '' : ":{$_SERVER['SERVER_PORT']}";
		$refToThisPage .= $serverPort . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		
		return $refToThisPage;
	}


} // End of Of Class

?>