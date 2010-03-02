<?php
// ===========================================================================================
//
// Class CWYSIWYGEditor
//
// Abstract base class WYSIWYG JavaScript editors as a replacement for <textarea>. 
// Each specific editor must inherit this class and implement its methods.
//
// Examples on usage of classes in a pagecontroller:
//  http://dev.phpersia.org/persia/?p=article-edit-all
//
//
// Author: Mikael Roos, mos@bth.se
//

abstract class CWYSIWYGEditor {

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
	public function __construct($aCSSId='none', $aCSSClass='none') { ; }


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