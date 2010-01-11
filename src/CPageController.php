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

		$val = isset($_GET["$aEntry"]) ? $_GET["$aEntry"] : $aDefault;
	}


	// ------------------------------------------------------------------------------------
	//
	// Connect to the database, return a database object.
	//
	public function ConnectToDatabase() {

		$this->iMysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

		if (mysqli_connect_error()) {
   			echo "Connect failed: ".mysqli_connect_error()."<br>";
   			exit();
		}

		return $this->iMysqli;
	}


	// ------------------------------------------------------------------------------------
	//
	// Execute a database multi_query
	//
	public function MultiQuery($aQuery) {

		$res = $this->iMysqli->multi_query($aQuery) 
                    or die("<p>Could not query database,</p><pre>{$aQuery}</pre>");

		return $res;
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Execute a database query
	//
	public function Query($aQuery) {

		$res = $this->iMysqli->query($aQuery) 
                    or die("<p>Could not query database,</p><pre>{$aQuery}</pre>");

		return $res;
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