<?php
// ===========================================================================================
//
// Class CWYSIWYGEditor_Plain
//
// A plain textarea editor, just for showing how to implement the abstract base class 
// for WYSIWYGEdito.
//
//
// Author: Mikael Roos, mos@bth.se
//

require_once('CWYSIWYGEditor.php');

class CWYSIWYGEditor_Plain extends CWYSIWYGEditor {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	public $iCSSId;		// A CSS id, if available
	public $iCSSClass;	// A CSS class, if available
	

	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct($aCSSId='none', $aCSSClass='none') {
		$this->iCSSId 		= $aCSSId; 
		$this->iCSSClass 	= $aCSSClass; 
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() { ; }


	// ------------------------------------------------------------------------------------
	//
	// Return the HTML header for the editor, usually stylesheet, js-file and javascript 
	// code to instantiate editor.
	//
	public function GetHTMLHead() { ; }
	

} // End of Of Class

?>