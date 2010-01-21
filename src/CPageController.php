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
	protected $iMysqli;


	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct() {

		$this->iMysqli = FALSE;		
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

		return isset($_GET["$aEntry"]) ? $_GET["$aEntry"] : $aDefault;
	}


	// ------------------------------------------------------------------------------------
	//
	// Check if corresponding $_POST[''] is set, then use it or return the default value.
	//
	public static function POSTisSetOrSetDefault($aEntry, $aDefault = '') {

		return isset($_POST["$aEntry"]) ? $_POST["$aEntry"] : $aDefault;
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


} // End of Of Class

?>