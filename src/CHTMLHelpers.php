<?php
// ===========================================================================================
//
// File: CHTMLHelpers.php
//
// Description: Class CHTMLHelpers
//
// Small code snippets to reduce coding in the pagecontrollers. The snippets are mainly for
// creating HTML code.
//
// Author: Mikael Roos, mos@bth.se
//


class CHTMLHelpers {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	

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
	// Create feedback notices if functions was successful or not. The messages are stored
	// in the session. This is useful in submitting form and providing user feedback.
	// This method reviews arrays of messages and stores them all in an resulting array.
	//
	public function GetHTMLForSessionMessages($aSuccessList, $aFailedList) {
	
		$imageLink = WS_IMAGES;
		$messages = Array();
		foreach($aSuccessList as $val) {
			$m = CPageController::GetSessionMessage($val);
			$messages[$val] = empty($m) ? '' : "<div class='userFeedbackPositive' style=\"background: url('{$imageLink}/silk/accept.png') no-repeat;\">{$m}</div>";
		}
		foreach($aFailedList as $val) {
			$m = CPageController::GetSessionMessage($val);
			$messages[$val] = empty($m) ? '' : "<div class='userFeedbackNegative' style=\"background: url('{$imageLink}/silk/cancel.png') no-repeat;\">{$m}</div>";
		}

		return $messages;
	}


} // End of Of Class


?>