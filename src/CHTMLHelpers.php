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
	// Create a positive (Ok/Success) feedback message for the user.
	//
	public static function GetHTMLForNavigationPath($aList) {
/*
<ul class='nav-standard nav-path'>
	<li><a href='{$link}ucp'>User control panel</a>
	<li>&gt; <a href='{$link}ucp'>ucp</a>
	<li>&gt; <a href='{$link}ucp'>ucp</a>
</ul>
*/

		return "<span class='userFeedbackPositive'>{$aMessage}</span>";
	}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Create a positive (Ok/Success) feedback message for the user.
	//
	public static function GetHTMLUserFeedbackPositive($aMessage) {
		return "<span class='userFeedbackPositive'>{$aMessage}</span>";
	}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Create a negative (Failed) feedback message for the user.
	//
	public static function GetHTMLUserFeedbackNegative($aMessage) {
		return "<span class='userFeedbackNegative'>{$aMessage}</span>";
	}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Create feedback notices if functions was successful or not. The messages are stored
	// in the session. This is useful in submitting form and providing user feedback.
	// This method reviews arrays of messages and stores them all in an resulting array.
	//
	public static function GetHTMLForSessionMessages($aSuccessList, $aFailedList) {
	
		$messages = Array();
		foreach($aSuccessList as $val) {
			$m = CPageController::GetAndClearSessionMessage($val);
			$messages[$val] = empty($m) ? '' : self::GetHTMLUserFeedbackPositive($m);
		}
		foreach($aFailedList as $val) {
			$m = CPageController::GetAndClearSessionMessage($val);
			$messages[$val] = empty($m) ? '' : self::GetHTMLUserFeedbackNegative($m);
		}

		return $messages;
	}


	// ------------------------------------------------------------------------------------
	//
	// Create a horisontal sidebar menu, a navgation bar, should be updated when nav
	// is improved in CSS.
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


/*
	// ------------------------------------------------------------------------------------
	//
	// Needs PHP5.3
	// Copied from http://se.php.net/manual/en/dateinterval.format.php#96768
	//
	// A sweet interval formatting, will use the two biggest interval parts.
	// On small intervals, you get minutes and seconds.
	// On big intervals, you get months and days.
	// Only the two biggest parts are used.
	//
	// @param DateTime $start
	// @param DateTime|null $end
	// @return string
	//
	public static function FormatDateDiff($start, $end=null) {
		if(!($start instanceof DateTime)) {
			$start = new DateTime($start);
		}
   
		if($end === null) {
			$end = new DateTime();
		}
   
		if(!($end instanceof DateTime)) {
			$end = new DateTime($start);
		}
   
		$interval = $end->diff($start);
		$doPlural = function($nb,$str){return $nb>1?$str.'s':$str;}; // adds plurals
		//$doPlural = create_function('$nb,$str', 'return $nb>1?$str."s":$str;'); // adds plurals
   
		$format = array();
		if($interval->y !== 0) {
			$format[] = "%y ".$doPlural($interval->y, "year");
		}
		if($interval->m !== 0) {
			$format[] = "%m ".$doPlural($interval->m, "month");
		}
		if($interval->d !== 0) {
			$format[] = "%d ".$doPlural($interval->d, "day");
		}
		if($interval->h !== 0) {
			$format[] = "%h ".$doPlural($interval->h, "hour");
		}
    if($interval->i !== 0) {
			$format[] = "%i ".$doPlural($interval->i, "minute");
		}
		if($interval->s !== 0) {
			if(!count($format)) {
				return "less than a minute ago";
			} else {
				$format[] = "%s ".$doPlural($interval->s, "second");
			}
		}
   
		// We use the two biggest parts
		if(count($format) > 1) {
			$format = array_shift($format)." and ".array_shift($format);
		} else {
			$format = array_pop($format);
		}
   
		// Prepend 'since ' or whatever you like
		return $interval->format($format);
	}
*/

} // End of Of Class


?>