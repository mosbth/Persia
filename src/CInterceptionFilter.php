<?php
// ===========================================================================================
//
// File: CInterceptionFilter.php
//
// Description: Class CInterceptionFilter
// Used in each pagecontroller to check access, authority.
//
//
// Author: Mikael Roos, mos@bth.se
//


class CInterceptionFilter {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	protected $iUc;


	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct() { 
		$this->iUc = CUserController::GetInstance();
	}


	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() { ; }


	// ------------------------------------------------------------------------------------
	//
	// Check if index.php (frontcontroller) is visited, disallow direct access to 
	// pagecontrollers
	//
	public function FrontControllerIsVisitedOrDie() {
		
		global $gPage; // Always defined in frontcontroller
		
		if(!isset($gPage)) {
			die('No direct access to pagecontroller is allowed.');
		}
	}


	// ------------------------------------------------------------------------------------
	//
	// Check if user has signed in or redirect user to sign in page
	//
	public function UserIsSignedInOrRecirectToSignIn() {
		
		if(!$this->iUc->IsAuthenticated()) { 
			require(TP_PAGESPATH . 'login/PLogin.php');
		}
	}


	// ------------------------------------------------------------------------------------
	//
	// Check if user belongs to the admin group, or die.
	//
	public function UserIsMemberOfGroupAdminOrDie() {
		
		if(!$this->iUc->IsAdministrator()) 
			die('You do not have the authourity to access this page');
	}


	// ------------------------------------------------------------------------------------
	//
	// Check if user belongs to the admin group or is a specific user.
	//
	public function IsUserMemberOfGroupAdminOrIsCurrentUser($aUserId) {
		
		$isAdmGroup 		= $this->iUc->IsAdministrator() ? true : false;
		$isCurrentUser	= ($this->iUc->GetAccountId() == $aUserId) ? true: false;

		return $isAdmGroup || $isCurrentUser;
	}


	// ------------------------------------------------------------------------------------
	//
	// Custom defined filter.
	// This method enables a custom filter by setting the $aLabel in the session.
	//
	// $aLabel: The label to set in the SESSION.
	// $aAction: check | set | unset
	//
	public function CustomFilterIsSetOrDie($aLabel, $aAction='check') {

		switch($aAction) {

			case 'set': {
				$_SESSION[$aLabel] = $aLabel;			
			} break;

			case 'unset': {
				unset($_SESSION[$aLabel]);
			} break;
		
			case 'check':
			default: {
				isset($_SESSION[$aLabel]) 
					or die('User defined filter not enabled. No access to this page.');
			} break;

		}
	}


} // End of Of Class

?>