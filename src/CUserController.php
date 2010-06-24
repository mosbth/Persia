<?php
// ===========================================================================================
//
// File: CUserController.php
//
// Description: Keep values for an authenticated user. This is used to hold information on the
// user. An object is instantiated and populated when the user loggs in. The object is stored
// in the session and used by pagecontrollers, together with CIntercetptionFilter, to verify 
// authority.
// Class is implemented as a Singelton-like where the getInstance gets the current instance from 
// $_SESSION. The constructor is public and is used to create a new instance when user signin
// or to create an empty instance for a unauthorized user.
//
// Author: Mikael Roos, mos@bth.se
//

class CUserController {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	protected static $iInstance = NULL;
	protected $iAccountId;
	protected $iAccountName;
	protected $iGroups;
	protected $iGravatar;


	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct() { ;	}


	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() { ; }


	// ------------------------------------------------------------------------------------
	//
	// Populate object when user signs in
	//
	public function Populate($aAccountId, $aAccountName, $aGroups, $aGravatar='') { 
		$this->iAccountId 	= $aAccountId;
		$this->iAccountName = $aAccountName;
		$this->iGroups 			= $aGroups;
		$this->iGravatar 		= $aGravatar;
	}


	// ------------------------------------------------------------------------------------
	//
	// Update parts of object
	//
	public function Update($aWhat, $aValue) { 
		switch($aWhat) {
			case 'gravatar': {$this->iGravatar = $aValue;} break;
		}
	}


	// ------------------------------------------------------------------------------------
	//
	// Store this object in the session
	//
	public function StoreInSession() { 
		$_SESSION['uc'] = $this;
	}


	// ------------------------------------------------------------------------------------
	//
	// Singleton, get the instance or create a new one.
	// The instance is stored in the session. If no instance is available the in the session,
	// then create a new object but do not store it in the session.
	//
	public static function GetInstance() {
		
		if(self::$iInstance == NULL) {
			if(isset($_SESSION['uc'])) {
				self::$iInstance = $_SESSION['uc'];
			} else {
				self::$iInstance = new CUserController();			
			}
		}
		return self::$iInstance;
	}


	// ------------------------------------------------------------------------------------
	//
	// Get the account id.
	//
	public function GetAccountId() {
		return $this->iAccountId;
	}


	// ------------------------------------------------------------------------------------
	//
	// Get the account name.
	//
	public function GetAccountName() {
		return $this->iAccountName;
	}


	// ------------------------------------------------------------------------------------
	//
	// Get a gravatar of the user.
	//
	public function GetGravatar() {
		return $this->iGravatar;
	}


	// ------------------------------------------------------------------------------------
	//
	// Is the user authenticated?
	//
	public function IsAuthenticated() {
		return empty($this->iAccountId) ? false : true;
	}


	// ------------------------------------------------------------------------------------
	//
	// Is the user a administrator?
	//
	public function IsAdministrator() {
		return $this->IsMemberOFGroup('admin');
	}


	// ------------------------------------------------------------------------------------
	//
	// Is member of a particulare group?
	//
	public function IsMemberOfGroup($aGroup) {
		return in_array($aGroup, $this->iGroups);
	}


}



?>